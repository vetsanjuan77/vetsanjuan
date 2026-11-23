<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Producto;
use App\Models\DetalleFactura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;

class InventarioStockWidget extends BaseWidget
{
    protected static ?string $heading = 'Análisis de Inventario y Stock';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        return $table
            ->query(
                Producto::query()
                    ->select([
                        'productos.*',
                        DB::raw('COALESCE(SUM(presentacion_sucursal.stock), 0) as stock_total'),
                        DB::raw('COALESCE(SUM(presentacion_sucursal.stock_minimo), 0) as stock_minimo_total'),
                        DB::raw('COALESCE(ventas_mes.cantidad_vendida, 0) as cantidad_vendida'),
                        DB::raw('COALESCE(ventas_mes.ingresos, 0) as ingresos'),
                        DB::raw('CASE 
                            WHEN COALESCE(SUM(presentacion_sucursal.stock), 0) > 0 AND COALESCE(ventas_mes.cantidad_vendida, 0) > 0 
                            THEN ROUND((COALESCE(ventas_mes.cantidad_vendida, 0) / COALESCE(SUM(presentacion_sucursal.stock), 0)) * 100, 2)
                            ELSE 0 
                        END as rotacion'),
                        DB::raw('CASE 
                            WHEN COALESCE(SUM(presentacion_sucursal.stock), 0) <= 0 THEN "sin_stock"
                            WHEN COALESCE(SUM(presentacion_sucursal.stock), 0) <= COALESCE(SUM(presentacion_sucursal.stock_minimo), 0) THEN "stock_bajo"
                            WHEN COALESCE(SUM(presentacion_sucursal.stock), 0) <= (COALESCE(SUM(presentacion_sucursal.stock_minimo), 0) * 2) THEN "stock_medio"
                            ELSE "stock_alto"
                        END as estado_stock')
                    ])
                    ->leftJoin('presentaciones', 'productos.id', '=', 'presentaciones.producto_id')
                    ->leftJoin('presentacion_sucursal', 'presentaciones.id', '=', 'presentacion_sucursal.presentacion_id')
                    ->leftJoin(DB::raw('(
                        SELECT 
                            df.producto_id,
                            SUM(df.cantidad) as cantidad_vendida,
                            SUM(df.cantidad * df.precio_unitario) as ingresos
                        FROM detalle_facturas df
                        INNER JOIN facturas f ON df.factura_id = f.id
                        WHERE f.created_at >= "' . Carbon::now()->startOfMonth()->format('Y-m-d H:i:s') . '"
                        ' . ($sucursalId ? 'AND f.sucursal_id = ' . $sucursalId : '') . '
                        GROUP BY df.producto_id
                    ) as ventas_mes'), 'productos.id', '=', 'ventas_mes.producto_id')
                    ->when($sucursalId, function($query) use ($sucursalId) {
                        $query->where('presentacion_sucursal.sucursal_id', $sucursalId);
                    })
                    ->groupBy('productos.id', 'productos.nombre', 'productos.description', 'productos.precio', 'productos.categoria', 'productos.especie', 'productos.marca_id', 'productos.edad', 'productos.imagen', 'productos.destacado', 'productos.cantidad_inventario', 'productos.created_at', 'productos.updated_at', 'ventas_mes.cantidad_vendida', 'ventas_mes.ingresos')
            )
            ->columns([
                TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                
                TextColumn::make('stock_total')
                    ->label('Stock Actual')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state, $record): string => match ($record->estado_stock) {
                        'sin_stock' => 'danger',
                        'stock_bajo' => 'warning',
                        'stock_medio' => 'info',
                        'stock_alto' => 'success',
                        default => 'gray',
                    }),
                
                TextColumn::make('stock_minimo_total')
                    ->label('Stock Mínimo')
                    ->sortable(),
                
                TextColumn::make('cantidad_vendida')
                    ->label('Vendido (Mes)')
                    ->sortable()
                    ->numeric(),
                
                TextColumn::make('rotacion')
                    ->label('Rotación (%)')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        $state >= 20 => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state . '%'),
                
                TextColumn::make('ingresos')
                    ->label('Ingresos (Mes)')
                    ->sortable()
                    ->money('COP')
                    ->color('success'),
                
                TextColumn::make('precio_venta')
                    ->label('Precio Venta')
                    ->sortable()
                    ->money('COP'),
                
                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 20) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_stock')
                    ->label('Estado de Stock')
                    ->options([
                        'sin_stock' => 'Sin Stock',
                        'stock_bajo' => 'Stock Bajo',
                        'stock_medio' => 'Stock Medio',
                        'stock_alto' => 'Stock Alto',
                    ]),
                
                Tables\Filters\SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'medicamentos' => 'Medicamentos',
                        'alimentos' => 'Alimentos',
                        'accesorios' => 'Accesorios',
                        'juguetes' => 'Juguetes',
                        'higiene' => 'Higiene',
                    ]),
                
                Tables\Filters\Filter::make('alta_rotacion')
                    ->label('Alta Rotación (>50%)')
                    ->query(fn ($query) => $query->havingRaw('rotacion > 50')),
                
                Tables\Filters\Filter::make('baja_rotacion')
                    ->label('Baja Rotación (<20%)')
                    ->query(fn ($query) => $query->havingRaw('rotacion < 20')),
            ])
            ->actions([
                Action::make('ver_detalle')
                    ->label('Ver Detalle')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Producto $record): string => route('filament.sucursal.resources.productos.view', $record))
                    ->openUrlInNewTab(),
                
                Action::make('crear_compra')
                    ->label('Crear Compra')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->visible(fn (Producto $record): bool => $record->stock_total <= $record->stock_minimo_total)
                    ->url(fn (Producto $record): string => route('filament.sucursal.resources.compras.create', ['producto' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('marcar_para_compra')
                    ->label('Marcar para Compra')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('warning')
                    ->action(function ($records) {
                        // Aquí se podría implementar lógica para marcar productos para compra
                        // Por ejemplo, crear una lista de compras o notificaciones
                    }),
            ])
            ->defaultSort('rotacion', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s')
            ->emptyStateHeading('No hay productos registrados')
            ->emptyStateDescription('Comienza agregando productos al inventario.')
            ->emptyStateIcon('heroicon-o-cube');
    }
}