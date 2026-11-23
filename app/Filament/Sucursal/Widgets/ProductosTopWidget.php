<?php

namespace App\Filament\Sucursal\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProductosTopWidget extends BaseWidget
{
    protected static ?string $heading = 'Productos Más Vendidos (Este Mes)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $esteMes = Carbon::now()->startOfMonth();

        return $table
            ->query(
                Producto::query()
                    ->select([
                        'productos.id',
                        'productos.nombre',
                        'productos.precio',
                        DB::raw('COALESCE(SUM(producto_sucursal.stock), 0) as stock_total'),
                        'productos.categoria',
                        DB::raw('COALESCE(SUM(venta_producto.cantidad), 0) as total_vendido'),
                        DB::raw('COALESCE(SUM(venta_producto.cantidad * venta_producto.sub_total / venta_producto.cantidad), 0) as ingresos_generados')
                    ])
                    ->leftJoin('producto_sucursal', 'productos.id', '=', 'producto_sucursal.producto_id')
                    ->leftJoin('venta_producto', 'productos.id', '=', 'venta_producto.producto_id')
                    ->leftJoin('venta', function($join) use ($esteMes, $sucursalId) {
                        $join->on('venta_producto.venta_id', '=', 'venta.id')
                             ->where('venta.created_at', '>=', $esteMes);
                        if ($sucursalId) {
                            $join->where('venta.sucursal_id', $sucursalId);
                        }
                    })
                    ->when($sucursalId, function($query) use ($sucursalId) {
                        $query->where('producto_sucursal.sucursal_id', $sucursalId);
                    })
                    ->groupBy([
                        'productos.id', 
                        'productos.nombre', 
                        'productos.precio', 
                        'productos.categoria'
                    ])
                    ->orderBy('total_vendido', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'medicamentos' => 'success',
                        'alimentos' => 'info',
                        'accesorios' => 'warning',
                        'servicios' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_vendido')
                    ->label('Cantidad Vendida')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio Unitario')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('ingresos_generados')
                    ->label('Ingresos Generados')
                    ->money('COP')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('stock_total')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(function (string $state): string {
                        return match (true) {
                            (int) $state === 0 => 'danger',
                            (int) $state <= 10 => 'warning',
                            default => 'success',
                        };
                    }),

                Tables\Columns\TextColumn::make('rotacion')
                    ->label('% Rotación')
                    ->getStateUsing(function ($record) {
                        if ($record->stock_total <= 0) return '0%';
                        $rotacion = ($record->total_vendido / ($record->stock_total + $record->total_vendido)) * 100;
                        return number_format($rotacion, 1) . '%';
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        $percentage = (float) str_replace('%', '', $state);
                        return match (true) {
                            $percentage >= 70 => 'success',
                            $percentage >= 40 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->alignCenter(),
            ])
            ->defaultSort('total_vendido', 'desc')
            ->striped()
            ->paginated(false)
            ->emptyStateHeading('Sin datos de ventas')
            ->emptyStateDescription('No hay productos vendidos en el período seleccionado.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }
}