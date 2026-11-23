<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\DetalleFactura;
use App\Models\CompraDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientosInventarioWidget extends ChartWidget
{
    protected static ?string $heading = 'Movimientos de Inventario (Últimos 30 Días)';
    protected static string $color = 'info';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $sucursalId = Auth::user()->sucursal_id ?? null;
        $fechaInicio = Carbon::now()->subDays(30);
        
        $fechas = [];
        $entradas = [];
        $salidas = [];
        $saldos = [];
        $saldoAcumulado = 0;

        // Generar datos para los últimos 30 días
        for ($i = 29; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $fechas[] = $fecha->format('d/m');
            
            // Entradas (compras)
            $entradasDia = CompraDetalle::whereHas('compra', function($query) use ($fecha, $sucursalId) {
                    $query->whereDate('created_at', $fecha->format('Y-m-d'))
                          ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId));
                })
                ->sum(DB::raw('cantidad_recibida * precio_unitario'));
            
            // Salidas (ventas)
            $salidasDia = DetalleFactura::whereHas('factura', function($query) use ($fecha, $sucursalId) {
                    $query->whereDate('created_at', $fecha->format('Y-m-d'))
                          ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId));
                })
                ->sum(DB::raw('cantidad * precio_unitario'));
            
            $entradas[] = (float) $entradasDia;
            $salidas[] = (float) $salidasDia;
            
            // Saldo acumulado
            $saldoAcumulado += $entradasDia - $salidasDia;
            $saldos[] = $saldoAcumulado;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entradas (Compras)',
                    'data' => $entradas,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Salidas (Ventas)',
                    'data' => $salidas,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Saldo Acumulado',
                    'data' => $saldos,
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'type' => 'line',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $fechas,
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
                        'text' => 'Valor Movimientos ($)',
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
                        'text' => 'Saldo Acumulado ($)',
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
                        'text' => 'Fecha',
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
                            let entradas = 0;
                            let salidas = 0;
                            tooltipItems.forEach(function(tooltipItem) {
                                if (tooltipItem.datasetIndex === 0) entradas = tooltipItem.parsed.y;
                                if (tooltipItem.datasetIndex === 1) salidas = tooltipItem.parsed.y;
                            });
                            let diferencia = entradas - salidas;
                            let tipo = diferencia >= 0 ? "Ganancia" : "Pérdida";
                            return ["", tipo + " del día: $" + Math.abs(diferencia).toLocaleString()];
                        }'
                    ]
                ],
            ],
        ];
    }
}