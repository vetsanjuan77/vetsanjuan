<?php

namespace App\Filament\Sucursal\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\Compra;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IngresosVsGastosWidget extends ChartWidget
{
    protected static ?string $heading = 'Ingresos vs Gastos (Últimos 12 Meses)';
    protected static string $color = 'warning';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        
        $meses = [];
        $ingresos = [];
        $gastos = [];
        $utilidad = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $meses[] = $fecha->format('M Y');
            
            // Ingresos del mes (facturas)
            $ingresosMes = Factura::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('total');
            
            // Gastos del mes (gastos + compras)
            $gastosMes = Gasto::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('valor');
            
            $comprasMes = Compra::whereYear('created_at', $fecha->year)
                ->whereMonth('created_at', $fecha->month)
                ->when($sucursalId, fn($query) => $query->where('sucursal_id', $sucursalId))
                ->sum('total');
            
            $totalGastos = $gastosMes + $comprasMes;
            
            $ingresos[] = (float) $ingresosMes;
            $gastos[] = (float) $totalGastos;
            $utilidad[] = (float) ($ingresosMes - $totalGastos);
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
                    'label' => 'Gastos',
                    'data' => $gastos,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Utilidad',
                    'data' => $utilidad,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'fill' => false,
                    'tension' => 0.4,
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
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Valor ($)',
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
                        'text' => 'Período',
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
                            let ingresos = 0;
                            let gastos = 0;
                            tooltipItems.forEach(function(tooltipItem) {
                                if (tooltipItem.datasetIndex === 0) ingresos = tooltipItem.parsed.y;
                                if (tooltipItem.datasetIndex === 1) gastos = tooltipItem.parsed.y;
                            });
                            let margen = ingresos > 0 ? ((ingresos - gastos) / ingresos * 100).toFixed(1) : 0;
                            return ["", "Margen: " + margen + "%"];
                        }'
                    ]
                ],
            ],
        ];
    }
}