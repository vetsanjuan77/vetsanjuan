<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Factura;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\DetalleFactura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentabilidadWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $fechaInicio = Carbon::now()->startOfMonth();
        $fechaInicioAnterior = Carbon::now()->subMonth()->startOfMonth();
        $fechaFinAnterior = Carbon::now()->subMonth()->endOfMonth();

        // Ingresos del mes actual
        $ingresosActuales = Factura::where('created_at', '>=', $fechaInicio)
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->where('tipo_venta', 'presencial')
            ->sum('total');

        // Ingresos del mes anterior
        $ingresosAnteriores = Factura::whereBetween('created_at', [$fechaInicioAnterior, $fechaFinAnterior])
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->where('tipo_venta', 'presencial')
            ->sum('total');

        // Costos del mes actual (compras + gastos)
        $comprasActuales = Compra::where('created_at', '>=', $fechaInicio)
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->sum('total');

        $gastosActuales = Gasto::where('created_at', '>=', $fechaInicio)
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->sum('monto');

        $costosActuales = $comprasActuales + $gastosActuales;

        // Costos del mes anterior
        $comprasAnteriores = Compra::whereBetween('created_at', [$fechaInicioAnterior, $fechaFinAnterior])
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->sum('total');

        $gastosAnteriores = Gasto::whereBetween('created_at', [$fechaInicioAnterior, $fechaFinAnterior])
            ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
            ->sum('monto');

        $costosAnteriores = $comprasAnteriores + $gastosAnteriores;

        // Utilidad bruta
        $utilidadBruta = $ingresosActuales - $costosActuales;
        $utilidadBrutaAnterior = $ingresosAnteriores - $costosAnteriores;

        // Margen de utilidad
        $margenUtilidad = $ingresosActuales > 0 ? ($utilidadBruta / $ingresosActuales) * 100 : 0;
        $margenUtilidadAnterior = $ingresosAnteriores > 0 ? ($utilidadBrutaAnterior / $ingresosAnteriores) * 100 : 0;

        // ROI (Return on Investment)
        $roi = $costosActuales > 0 ? ($utilidadBruta / $costosActuales) * 100 : 0;
        $roiAnterior = $costosAnteriores > 0 ? ($utilidadBrutaAnterior / $costosAnteriores) * 100 : 0;

        // Punto de equilibrio (estimado)
        $gastosOperativos = $gastosActuales;
        $margenContribucion = $ingresosActuales > 0 ? (($ingresosActuales - $comprasActuales) / $ingresosActuales) : 0;
        $puntoEquilibrio = $margenContribucion > 0 ? $gastosOperativos / $margenContribucion : 0;

        // Cálculo de tendencias
        $tendenciaIngresos = $ingresosAnteriores > 0 ? (($ingresosActuales - $ingresosAnteriores) / $ingresosAnteriores) * 100 : 0;
        $tendenciaUtilidad = $utilidadBrutaAnterior != 0 ? (($utilidadBruta - $utilidadBrutaAnterior) / abs($utilidadBrutaAnterior)) * 100 : 0;
        $tendenciaMargen = $margenUtilidadAnterior != 0 ? (($margenUtilidad - $margenUtilidadAnterior) / abs($margenUtilidadAnterior)) * 100 : 0;
        $tendenciaROI = $roiAnterior != 0 ? (($roi - $roiAnterior) / abs($roiAnterior)) * 100 : 0;

        return [
            Stat::make('Utilidad Bruta (Mes)', '$' . number_format($utilidadBruta, 0))
                ->description($tendenciaUtilidad >= 0 ? 
                    '+' . number_format(abs($tendenciaUtilidad), 1) . '% vs mes anterior' : 
                    '-' . number_format(abs($tendenciaUtilidad), 1) . '% vs mes anterior')
                ->descriptionIcon($tendenciaUtilidad >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($utilidadBruta >= 0 ? 'success' : 'danger')
                ->chart($this->getUtilidadChart()),

            Stat::make('Margen de Utilidad', number_format($margenUtilidad, 1) . '%')
                ->description($tendenciaMargen >= 0 ? 
                    '+' . number_format(abs($tendenciaMargen), 1) . '% vs mes anterior' : 
                    '-' . number_format(abs($tendenciaMargen), 1) . '% vs mes anterior')
                ->descriptionIcon($tendenciaMargen >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($margenUtilidad >= 20 ? 'success' : ($margenUtilidad >= 10 ? 'warning' : 'danger')),

            Stat::make('ROI (Retorno Inversión)', number_format($roi, 1) . '%')
                ->description($tendenciaROI >= 0 ? 
                    '+' . number_format(abs($tendenciaROI), 1) . '% vs mes anterior' : 
                    '-' . number_format(abs($tendenciaROI), 1) . '% vs mes anterior')
                ->descriptionIcon($tendenciaROI >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($roi >= 30 ? 'success' : ($roi >= 15 ? 'warning' : 'danger')),

            Stat::make('Punto de Equilibrio', '$' . number_format($puntoEquilibrio, 0))
                ->description($ingresosActuales >= $puntoEquilibrio ? 
                    'Meta alcanzada ✅' : 
                    'Faltan $' . number_format($puntoEquilibrio - $ingresosActuales, 0))
                ->descriptionIcon($ingresosActuales >= $puntoEquilibrio ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($ingresosActuales >= $puntoEquilibrio ? 'success' : 'warning'),
        ];
    }

    private function getUtilidadChart(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $datos = [];

        // Últimos 7 días de utilidad
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            
            $ingresos = Factura::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            $compras = Compra::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('total');
            
            $gastos = Gasto::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('monto');
            
            $utilidad = $ingresos - $compras - $gastos;
            $datos[] = $utilidad;
        }

        return $datos;
    }
}