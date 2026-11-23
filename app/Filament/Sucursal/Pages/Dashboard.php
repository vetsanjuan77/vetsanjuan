<?php

namespace App\Filament\Sucursal\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.sucursal.pages.dashboard';

    public function getTitle(): string
    {
        $sucursal = Auth::user()->sucursal;
        return $sucursal ? "Dashboard - {$sucursal->nombre}" : 'Dashboard';
    }

    public function getSubheading(): ?string
    {
        $sucursal = Auth::user()->sucursal;
        return $sucursal ? "Código: {$sucursal->codigo} | {$sucursal->ciudad}, {$sucursal->departamento}" : null;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Sucursal\Widgets\EstadisticasSucursalWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Sucursal\Widgets\VentasChartWidget::class,
            \App\Filament\Sucursal\Widgets\ProductosTopWidget::class,
            \App\Filament\Sucursal\Widgets\IngresosVsGastosWidget::class,
            \App\Filament\Sucursal\Widgets\AlertasInventarioWidget::class,
        ];
    }
}