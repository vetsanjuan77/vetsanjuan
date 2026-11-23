<?php

namespace App\Filament\Sucursal\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EstadisticasSucursalWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $hoy = Carbon::today();
        $esteMes = Carbon::now()->startOfMonth();
        $mesAnterior = Carbon::now()->subMonth();
        
        // Obtener sucursal del usuario actual (si aplica)
        $sucursalId = Auth::user()->sucursal_id ?? null;

        // Ventas de hoy
        $ventasHoyQuery = Factura::whereDate('created_at', $hoy);
        if ($sucursalId) {
            $ventasHoyQuery->where('sucursal_id', $sucursalId);
        }
        $ventasHoy = $ventasHoyQuery->sum('total');
        $facturasHoy = $ventasHoyQuery->count();

        // Ventas del mes
        $ventasMesQuery = Factura::where('created_at', '>=', $esteMes);
        if ($sucursalId) {
            $ventasMesQuery->where('sucursal_id', $sucursalId);
        }
        $ventasMes = $ventasMesQuery->sum('total');

        // Ventas mes anterior para comparación
        $ventasMesAnteriorQuery = Factura::whereMonth('created_at', $mesAnterior->month)
            ->whereYear('created_at', $mesAnterior->year);
        if ($sucursalId) {
            $ventasMesAnteriorQuery->where('sucursal_id', $sucursalId);
        }
        $ventasMesAnterior = $ventasMesAnteriorQuery->sum('total');

        // Calcular crecimiento
        $crecimiento = $ventasMesAnterior > 0 
            ? (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100 
            : 0;

        // Clientes nuevos este mes
        $clientesNuevos = Cliente::where('created_at', '>=', $esteMes)->count();

        // Productos con stock bajo
        $productosStockBajo = Producto::where('cantidad_inventario', '<', 10)
            ->where('cantidad_inventario', '>', 0)
            ->count();

        // Producto más vendido del mes
        $productoMasVendido = DB::table('venta_producto')
            ->join('venta', 'venta_producto.venta_id', '=', 'venta.id')
            ->join('productos', 'venta_producto.producto_id', '=', 'productos.id')
            ->where('venta.created_at', '>=', $esteMes)
            ->when($sucursalId, fn($query) => $query->where('venta.sucursal_id', $sucursalId))
            ->select('productos.nombre', DB::raw('SUM(venta_producto.cantidad) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total', 'desc')
            ->first();

        // Flujo de caja del día
        $movimientosCajaQuery = MovimientoCaja::whereDate('created_at', $hoy);
        if ($sucursalId) {
            $movimientosCajaQuery->whereHas('caja', function($query) use ($sucursalId) {
                $query->where('sucursal_id', $sucursalId);
            });
        }
        
        $ingresos = $movimientosCajaQuery->where('tipo', 'ingreso')->sum('valor');
        $egresos = $movimientosCajaQuery->where('tipo', 'egreso')->sum('valor');
        $saldoCaja = $ingresos - $egresos;

        // Gastos del mes
        $gastosQuery = Gasto::where('created_at', '>=', $esteMes);
        if ($sucursalId) {
            $gastosQuery->where('sucursal_id', $sucursalId);
        }
        $gastosMes = $gastosQuery->sum('valor');

        return [
            Stat::make('Ventas Hoy', '$' . number_format($ventasHoy, 0, ',', '.'))
                ->description($facturasHoy . ' facturas generadas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getVentasUltimos7Dias()),

            Stat::make('Ventas Este Mes', '$' . number_format($ventasMes, 0, ',', '.'))
                ->description(
                    ($crecimiento >= 0 ? '+' : '') . 
                    number_format($crecimiento, 1) . '% vs mes anterior'
                )
                ->descriptionIcon($crecimiento >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($crecimiento >= 0 ? 'success' : 'danger'),

            Stat::make('Clientes Nuevos', $clientesNuevos)
                ->description('Registrados este mes')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Stock Bajo', $productosStockBajo)
                ->description('Productos con menos de 10 unidades')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($productosStockBajo > 0 ? 'warning' : 'success'),

            Stat::make('Producto Top', $productoMasVendido ? substr($productoMasVendido->nombre, 0, 15) . '...' : 'Sin datos')
                ->description($productoMasVendido ? $productoMasVendido->total . ' unidades vendidas' : 'Este mes')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),

            Stat::make('Saldo Caja Hoy', '$' . number_format($saldoCaja, 0, ',', '.'))
                ->description('Ingresos: $' . number_format($ingresos, 0, ',', '.') . ' | Egresos: $' . number_format($egresos, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($saldoCaja >= 0 ? 'success' : 'danger'),
        ];
    }

    private function getVentasUltimos7Dias(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        $ventas = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i)->startOfDay();
            $ventasDia = Factura::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('total');
            $ventas[] = (int) ($ventasDia / 1000); // Simplificar para el gráfico
        }
        
        return $ventas;
    }

    protected function getColumns(): int
    {
        return 3;
    }
}