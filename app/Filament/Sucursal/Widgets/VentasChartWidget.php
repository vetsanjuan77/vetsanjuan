<?php

namespace App\Filament\Sucursal\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VentasChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas Últimos 30 Días';
    protected static string $color = 'info';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        // Obtener datos de los últimos 30 días
        $fechas = [];
        $ventas = [];
        $facturas = [];

        for ($i = 29; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $fechas[] = $fecha->format('d/m');
            
            $ventasDia = Factura::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->sum('total');
            
            $facturasDia = Factura::whereDate('created_at', $fecha)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->where('tipo_venta', 'presencial')
                ->count();
            
            $ventas[] = (float) $ventasDia;
            $facturas[] = $facturasDia;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas ($)',
                    'data' => $ventas,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Facturas',
                    'data' => $facturas,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
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
                            if (context.datasetIndex === 0) {
                                label += "$" + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }'
                    ]
                ],
            ],
        ];
    }
}