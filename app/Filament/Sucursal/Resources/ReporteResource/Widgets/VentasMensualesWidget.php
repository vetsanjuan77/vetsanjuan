<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VentasMensualesWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas Mensuales - Últimos 12 Meses';
    protected static string $color = 'warning';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        $meses = [];
        $ventas = [];
        $facturas = [];
        $meta = [];

        // Meta mensual (se puede configurar desde la base de datos)
        $metaMensual = 5000000; // $5,000,000 COP como ejemplo

        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $meses[] = $fecha->format('M Y');
            
            // Ventas del mes
            $ventasMes = Factura::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            // Número de facturas del mes
            $facturasMes = Factura::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->count();
            
            $ventas[] = (float) $ventasMes;
            $facturas[] = $facturasMes;
            $meta[] = $metaMensual;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas Reales ($)',
                    'data' => $ventas,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                    'type' => 'bar',
                ],
                [
                    'label' => 'Meta Mensual ($)',
                    'data' => $meta,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => false,
                    'tension' => 0,
                    'type' => 'line',
                    'borderDash' => [10, 5],
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Número de Facturas',
                    'data' => $facturas,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'type' => 'line',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $meses,
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
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Ventas ($)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) {
                            return "$" + (value / 1000000).toFixed(1) + "M";
                        }'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Número de Facturas',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
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
                            if (context.datasetIndex === 2) {
                                label += context.parsed.y + " facturas";
                            } else {
                                label += "$" + context.parsed.y.toLocaleString();
                            }
                            return label;
                        }',
                        'afterBody' => 'function(tooltipItems) {
                            let ventas = 0;
                            let meta = 0;
                            tooltipItems.forEach(function(tooltipItem) {
                                if (tooltipItem.datasetIndex === 0) ventas = tooltipItem.parsed.y;
                                if (tooltipItem.datasetIndex === 1) meta = tooltipItem.parsed.y;
                            });
                            if (meta > 0) {
                                let cumplimiento = (ventas / meta * 100).toFixed(1);
                                return ["", "Cumplimiento de meta: " + cumplimiento + "%"];
                            }
                            return [];
                        }'
                    ]
                ],
            ],
        ];
    }
}