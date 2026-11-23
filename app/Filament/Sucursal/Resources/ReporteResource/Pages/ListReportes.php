<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Pages;

use App\Filament\Sucursal\Resources\ReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListReportes extends ListRecords
{
    protected static string $resource = ReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generar_reporte')
                ->label('Generar Nuevo Reporte')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(fn () => static::getResource()::getUrl('generar')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReporteResource\Widgets\ResumenVentasWidget::class,
        ];
    }
}