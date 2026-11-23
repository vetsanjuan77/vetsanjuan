<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use App\Models\Compra;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlujoCajaWidget extends ChartWidget
{
    protected static ?string $heading = 'Flujo de Caja (Últimos 12 Meses)';
    protected static string $color = 'success';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        $meses = [];
        $ingresos = [];
        $egresos = [];
        $flujoNeto = [];

        // Generar datos para los últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $meses[] = $fecha->format('M Y');
            
            // Ingresos del mes (ventas)
            $ingresosMes = Factura::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('total');
            
            // Egresos del mes (compras + gastos)
            $comprasMes = Compra::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('total');
            
            $gastosMes = Gasto::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('monto');
            
            $egresosMes = $comprasMes + $gastosMes;
            $flujoNetoMes = $ingresosMes - $egresosMes;
            
            $ingresos[] = (float) $ingresosMes;
            $egresos[] = (float) $egresosMes;
            $flujoNeto[] = (float) $flujoNetoMes;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $ingresos,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Egresos',
                    'data' => $egresos,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Flujo Neto',
                    'data' => $flujoNeto,
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => function($context) {
                        return $context.parsed.y >= 0 ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)';
                    },
                    'fill' => true,
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
                        'text' => 'Ingresos y Egresos ($)',
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
                        'text' => 'Flujo Neto ($)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
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
                        'text' => 'Mes',
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
                            let value = context.parsed.y;
                            if (context.datasetIndex === 2) {
                                // Flujo neto - mostrar color según positivo/negativo
                                let color = value >= 0 ? "🟢" : "🔴";
                                label = color + " " + label;
                            }
                            label += "$" + Math.abs(value).toLocaleString();
                            return label;
                        }',
                        'afterBody' => 'function(tooltipItems) {
                            let ingresos = 0;
                            let egresos = 0;
                            tooltipItems.forEach(function(tooltipItem) {
                                if (tooltipItem.datasetIndex === 0) ingresos = tooltipItem.parsed.y;
                                if (tooltipItem.datasetIndex === 1) egresos = tooltipItem.parsed.y;
                            });
                            if (ingresos > 0 && egresos > 0) {
                                let margen = ((ingresos - egresos) / ingresos * 100).toFixed(1);
                                return ["", "Margen: " + margen + "%"];
                            }
                            return [];
                        }'
                    ]
                ],
            ],
        ];
    }
}