<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Pages;

use App\Filament\Sucursal\Resources\ReporteResource;
use Filament\Resources\Pages\Page;
use Filament\Widgets\ChartWidget;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteVentas extends Page
{
    protected static string $resource = ReporteResource::class;

    protected static string $view = 'filament.sucursal.resources.reporte-resource.pages.reporte-ventas';

    protected static ?string $title = 'Reportes de Ventas';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\VentasDiariasWidget::class,
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\VentasSemanalesWidget::class,
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\VentasMensualesWidget::class,
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\VentasPorHoraWidget::class,
        ];
    }
}

// Widget para gráfico de ventas diarias
class VentasDiariasChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas Últimos 30 Días';

    protected function getData(): array
    {
        $ventasDiarias = Factura::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as fecha, SUM(total) as total_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Ventas Diarias',
                    'data' => $ventasDiarias->pluck('total_ventas')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $ventasDiarias->pluck('fecha')->map(function ($fecha) {
                return Carbon::parse($fecha)->format('d/m');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

// Widget para gráfico de ventas mensuales
class VentasMensualesChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Mes (Último Año)';

    protected function getData(): array
    {
        $ventasMensuales = Factura::where('created_at', '>=', now()->subYear())
            ->selectRaw('YEAR(created_at) as año, MONTH(created_at) as mes, SUM(total) as total_ventas')
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Ventas Mensuales',
                    'data' => $ventasMensuales->pluck('total_ventas')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)',
                        'rgba(83, 102, 255, 0.2)',
                        'rgba(255, 99, 255, 0.2)',
                        'rgba(99, 255, 132, 0.2)',
                        'rgba(255, 199, 99, 0.2)',
                        'rgba(132, 99, 255, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(255, 99, 255, 1)',
                        'rgba(99, 255, 132, 1)',
                        'rgba(255, 199, 99, 1)',
                        'rgba(132, 99, 255, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $ventasMensuales->map(function ($venta) {
                return Carbon::createFromDate($venta->año, $venta->mes, 1)->format('M Y');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

// Widget para productos más vendidos
class ProductosMasVendidosChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Productos Más Vendidos (Este Mes)';

    protected function getData(): array
    {
        $productosVendidos = DB::table('detalle_facturas')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->where('facturas.created_at', '>=', now()->startOfMonth())
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_facturas.cantidad) as total_vendido')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad Vendida',
                    'data' => $productosVendidos->pluck('total_vendido')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 99, 255, 0.8)',
                        'rgba(99, 255, 132, 0.8)',
                    ],
                ],
            ],
            'labels' => $productosVendidos->pluck('nombre')->map(function ($nombre) {
                return strlen($nombre) > 20 ? substr($nombre, 0, 20) . '...' : $nombre;
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}