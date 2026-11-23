<?php

namespace App\Filament\Sucursal\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlertasInventarioWidget extends BaseWidget
{
    protected static ?string $heading = 'Alertas de Inventario';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $sucursalId = Auth::user()->sucursal_id;
        
        return $table
            ->query(
                Producto::query()
                    ->select([
                        'productos.id',
                        'productos.codigo',
                        'productos.nombre',
                        'productos.categoria',
                        'productos.cantidad_inventario',
                        'productos.created_at',
                        DB::raw('COALESCE(SUM(presentacion_sucursal.stock), 0) as stock_total')
                    ])
                    ->join('presentaciones', 'productos.id', '=', 'presentaciones.producto_id')
                    ->leftJoin('presentacion_sucursal', function($join) use ($sucursalId) {
                        $join->on('presentaciones.id', '=', 'presentacion_sucursal.presentacion_id')
                             ->where('presentacion_sucursal.sucursal_id', '=', $sucursalId);
                    })
                    ->groupBy('productos.id', 'productos.codigo', 'productos.nombre', 'productos.categoria', 'productos.cantidad_inventario', 'productos.created_at')
                    ->havingRaw('COALESCE(SUM(presentacion_sucursal.stock), 0) <= 10')
                    ->orderByRaw('CASE WHEN COALESCE(SUM(presentacion_sucursal.stock), 0) = 0 THEN 1 ELSE 2 END')
                    ->orderByRaw('COALESCE(SUM(presentacion_sucursal.stock), 0) ASC')
            )
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'medicamentos' => 'success',
                        'alimentos' => 'info',
                        'accesorios' => 'warning',
                        'juguetes' => 'primary',
                        'higiene' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('stock_total')
                    ->label('Stock Actual')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state == 0 => 'danger',
                        $state <= 5 => 'warning',
                        $state <= 10 => 'info',
                        default => 'success',
                    })
                    ->icon(fn ($state): string => match (true) {
                        $state == 0 => 'heroicon-o-x-circle',
                        $state <= 5 => 'heroicon-o-exclamation-triangle',
                        $state <= 10 => 'heroicon-o-information-circle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->alignCenter()
                    ->default(10)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('Precio')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->wrap()
                    ->placeholder('Sin proveedor')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('estado_alerta')
                    ->label('Estado')
                    ->getStateUsing(function ($record) {
                        if ($record->stock_total === 0) {
                            return 'SIN STOCK';
                        } elseif ($record->stock_total <= 5) {
                            return 'CRÍTICO';
                        } elseif ($record->stock_total <= 10) {
                            return 'BAJO';
                        }
                        return 'NORMAL';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SIN STOCK' => 'danger',
                        'CRÍTICO' => 'warning',
                        'BAJO' => 'info',
                        'NORMAL' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('accion_sugerida')
                    ->label('Acción Sugerida')
                    ->getStateUsing(function ($record) {
                        if ($record->stock_total === 0) {
                            return 'Reabastecer urgente';
                        } elseif ($record->stock_total <= 5) {
                            return 'Realizar pedido';
                        } elseif ($record->stock_total <= 10) {
                            return 'Planificar compra';
                        }
                        return 'Monitorear';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Reabastecer urgente' => 'danger',
                        'Realizar pedido' => 'warning',
                        'Planificar compra' => 'info',
                        'Monitorear' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('crear_compra')
                    ->label('Crear Compra')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->url(fn ($record) => route('filament.sucursal.resources.compras.create', [
                        'producto_id' => $record->id
                    ]))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('ver_producto')
                    ->label('Ver Producto')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.sucursal.resources.productos.view', [
                        'record' => $record->id
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('marcar_para_compra')
                    ->label('Marcar para Compra')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->action(function ($records) {
                        // Aquí se podría implementar lógica para crear una lista de compras
                        // o redirigir a una página de compras múltiples
                        session()->flash('success', 'Productos marcados para compra: ' . $records->count());
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Marcar productos para compra')
                    ->modalDescription('¿Desea marcar estos productos para crear una orden de compra?')
                    ->modalSubmitActionLabel('Marcar'),
            ])
            ->defaultSort('cantidad_inventario', 'asc')
            ->striped()
            ->paginated([10, 25, 50])
            ->emptyStateHeading('¡Excelente!')
            ->emptyStateDescription('No hay productos con stock bajo en este momento.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->poll('30s'); // Actualizar cada 30 segundos
    }
}