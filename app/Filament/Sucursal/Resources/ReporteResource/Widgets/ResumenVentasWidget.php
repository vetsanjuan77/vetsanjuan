<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Factura;
use App\Models\MovimientoCaja;
use App\Models\Gasto;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResumenVentasWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $hoy = Carbon::today();
        $esteMes = Carbon::now()->startOfMonth();
        $mesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        // Ventas de hoy
        $ventasHoy = Factura::whereDate('created_at', Carbon::today())
            ->where('tipo_venta', 'presencial')
            ->sum('total');

        // Ventas del mes actual
        $ventasEsteMes = Factura::where('created_at', '>=', $esteMes)
            ->sum('total');

        // Ventas del mes anterior para comparación
        $ventasMesAnterior = Factura::whereBetween('created_at', [$mesAnterior, $finMesAnterior])
            ->sum('total');

        // Calcular porcentaje de cambio
        $cambioMensual = $ventasMesAnterior > 0 
            ? (($ventasEsteMes - $ventasMesAnterior) / $ventasMesAnterior) * 100 
            : 0;

        // Número de facturas hoy
        $facturasHoy = Factura::whereDate('created_at', Carbon::today())
            ->where('tipo_venta', 'presencial')
            ->count();

        // Productos con stock bajo (menos de 10 unidades)
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $productosStockBajo = DB::table('productos')
            ->leftJoin('presentaciones', 'productos.id', '=', 'presentaciones.producto_id')
            ->leftJoin('presentacion_sucursal', function($join) use ($sucursalId) {
                $join->on('presentaciones.id', '=', 'presentacion_sucursal.presentacion_id')
                     ->where('presentacion_sucursal.sucursal_id', '=', $sucursalId);
            })
            ->select('productos.id')
            ->groupBy('productos.id')
            ->havingRaw('COALESCE(SUM(presentacion_sucursal.stock), 0) < 10')
            ->havingRaw('COALESCE(SUM(presentacion_sucursal.stock), 0) > 0')
            ->count();

        // Flujo de caja del día
        $ingresosCaja = MovimientoCaja::whereDate('created_at', $hoy)
            ->where('tipo', 'ingreso')
            ->sum('valor');

        $egresosCaja = MovimientoCaja::whereDate('created_at', $hoy)
            ->where('tipo', 'egreso')
            ->sum('valor');

        $flujoCaja = $ingresosCaja - $egresosCaja;

        // Gastos del mes
        $gastosEsteMes = Gasto::where('created_at', '>=', $esteMes)
            ->sum('monto');

        return [
            Stat::make('Ventas Hoy', '$' . number_format($ventasHoy, 0, ',', '.'))
                ->description($facturasHoy . ' facturas generadas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Ventas Este Mes', '$' . number_format($ventasEsteMes, 0, ',', '.'))
                ->description(
                    ($cambioMensual >= 0 ? '+' : '') . 
                    number_format($cambioMensual, 1) . '% vs mes anterior'
                )
                ->descriptionIcon($cambioMensual >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($cambioMensual >= 0 ? 'success' : 'danger'),

            Stat::make('Flujo de Caja Hoy', '$' . number_format($flujoCaja, 0, ',', '.'))
                ->description('Ingresos: $' . number_format($ingresosCaja, 0, ',', '.') . ' | Egresos: $' . number_format($egresosCaja, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($flujoCaja >= 0 ? 'success' : 'warning'),

            Stat::make('Productos Stock Bajo', $productosStockBajo)
                ->description('Productos con menos de 10 unidades')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($productosStockBajo > 0 ? 'warning' : 'success'),

            Stat::make('Gastos Este Mes', '$' . number_format($gastosEsteMes, 0, ',', '.'))
                ->description('Total de gastos operativos')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('danger'),

            Stat::make('Utilidad Estimada', '$' . number_format($ventasEsteMes - $gastosEsteMes, 0, ',', '.'))
                ->description('Ventas menos gastos del mes')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($ventasEsteMes - $gastosEsteMes >= 0 ? 'success' : 'danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}