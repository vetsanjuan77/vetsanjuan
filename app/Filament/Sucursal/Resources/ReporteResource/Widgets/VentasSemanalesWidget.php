<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VentasSemanalesWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas Semanales - Últimas 8 Semanas';
    protected static string $color = 'info';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        $semanas = [];
        $ventasActuales = [];
        $ventasAnteriores = [];

        for ($i = 7; $i >= 0; $i--) {
            $inicioSemana = Carbon::now()->subWeeks($i)->startOfWeek();
            $finSemana = Carbon::now()->subWeeks($i)->endOfWeek();
            
            // Semana anterior para comparación
            $inicioSemanaAnterior = $inicioSemana->copy()->subWeek();
            $finSemanaAnterior = $finSemana->copy()->subWeek();
            
            $semanas[] = $inicioSemana->format('d/m') . ' - ' . $finSemana->format('d/m');
            
            // Ventas de la semana actual
            $ventasSemana = Factura::whereBetween('created_at', [$inicioSemana, $finSemana])
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            // Ventas de la semana anterior (para comparación)
            $ventasSemanaAnterior = Factura::whereBetween('created_at', [$inicioSemanaAnterior, $finSemanaAnterior])
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            $ventasActuales[] = (float) $ventasSemana;
            $ventasAnteriores[] = (float) $ventasSemanaAnterior;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas Actuales',
                    'data' => $ventasActuales,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Semana Anterior (Comparación)',
                    'data' => $ventasAnteriores,
                    'backgroundColor' => 'rgba(156, 163, 175, 0.6)',
                    'borderColor' => 'rgb(156, 163, 175)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $semanas,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Ventas ($)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) {
                            return "$" + value.toLocaleString();
                        }'
                    ]
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Período Semanal',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += "$" + context.parsed.y.toLocaleString();
                            return label;
                        }',
                        'afterBody' => 'function(tooltipItems) {
                            if (tooltipItems.length >= 2) {
                                let actual = tooltipItems[0].parsed.y;
                                let anterior = tooltipItems[1].parsed.y;
                                if (anterior > 0) {
                                    let crecimiento = ((actual - anterior) / anterior * 100).toFixed(1);
                                    return ["", "Crecimiento: " + (crecimiento >= 0 ? "+" : "") + crecimiento + "%"];
                                }
                            }
                            return [];
                        }'
                    ]
                ],
            ],
        ];
    }
}