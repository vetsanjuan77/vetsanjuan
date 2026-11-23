<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentasPorHoraWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Hora del Día (Últimos 30 Días)';
    protected static string $color = 'primary';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $fechaInicio = Carbon::now()->subDays(30);
        
        $horas = [];
        $ventas = [];
        $facturas = [];

        // Generar datos para cada hora del día (6 AM a 10 PM)
        for ($hora = 6; $hora <= 22; $hora++) {
            $horas[] = $hora . ':00';
            
            // Ventas por hora
            $ventasHora = Factura::where('created_at', '>=', $fechaInicio)
                ->whereRaw('HOUR(created_at) = ?', [$hora])
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            // Número de facturas por hora
            $facturasHora = Factura::where('created_at', '>=', $fechaInicio)
                ->whereRaw('HOUR(created_at) = ?', [$hora])
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->count();
            
            $ventas[] = (float) $ventasHora;
            $facturas[] = $facturasHora;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas por Hora ($)',
                    'data' => $ventas,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Facturas por Hora',
                    'data' => $facturas,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'type' => 'line',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $horas,
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
                            return "$" + value.toLocaleString();
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
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Hora del Día',
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
                            if (context.datasetIndex === 1) {
                                label += context.parsed.y + " facturas";
                            } else {
                                label += "$" + context.parsed.y.toLocaleString();
                            }
                            return label;
                        }',
                        'afterBody' => 'function(tooltipItems) {
                            let ventas = 0;
                            let facturas = 0;
                            tooltipItems.forEach(function(tooltipItem) {
                                if (tooltipItem.datasetIndex === 0) ventas = tooltipItem.parsed.y;
                                if (tooltipItem.datasetIndex === 1) facturas = tooltipItem.parsed.y;
                            });
                            if (facturas > 0) {
                                let promedio = (ventas / facturas).toFixed(0);
                                return ["", "Promedio por factura: $" + parseInt(promedio).toLocaleString()];
                            }
                            return [];
                        }'
                    ]
                ],
            ],
        ];
    }
}