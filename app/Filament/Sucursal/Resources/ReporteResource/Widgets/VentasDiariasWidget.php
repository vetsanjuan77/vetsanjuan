<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VentasDiariasWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas Diarias - Últimos 15 Días';
    protected static string $color = 'success';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        $fechas = [];
        $ventas = [];
        $facturas = [];
        $promedioVenta = [];

        for ($i = 14; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $fechas[] = $fecha->format('d/m');
            
            // Ventas del día
            $ventasDia = Factura::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            // Número de facturas
            $facturasDia = Factura::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->count();
            
            // Promedio por factura
            $promedio = $facturasDia > 0 ? $ventasDia / $facturasDia : 0;
            
            $ventas[] = (float) $ventasDia;
            $facturas[] = $facturasDia;
            $promedioVenta[] = (float) $promedio;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas Totales ($)',
                    'data' => $ventas,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Número de Facturas',
                    'data' => $facturas,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
                [
                    'label' => 'Promedio por Factura ($)',
                    'data' => $promedioVenta,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $fechas,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                                label += context.parsed.y;
                            } else {
                                label += "$" + context.parsed.y.toLocaleString();
                            }
                            return label;
                        }'
                    ]
                ],
            ],
        ];
    }
}