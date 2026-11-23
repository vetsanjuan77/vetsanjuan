<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Pages;

use App\Filament\Sucursal\Resources\ReporteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteFinancieroExport;

class ReporteFinanciero extends Page
{
    protected static string $resource = ReporteResource::class;

    protected static string $view = 'filament.sucursal.resources.reporte-resource.pages.reporte-financiero';

    protected static ?string $title = 'Reportes Financieros';
    
    protected static ?string $navigationLabel = 'Financiero';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\RentabilidadWidget::class,
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\FlujoCajaWidget::class,
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\GastosDetalladosWidget::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('estado_resultados')
                ->label('Estado de Resultados')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->url(route('reports.estado_resultados'))
                ->openUrlInNewTab(),
            
            Actions\Action::make('balance_general')
                ->label('Balance General')
                ->icon('heroicon-o-scale')
                ->color('warning')
                ->url(route('reports.balance_general'))
                ->openUrlInNewTab(),
            
            Actions\Action::make('exportar_excel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('exports.financiero.excel'))
                ->openUrlInNewTab(),
            
            Actions\Action::make('exportar_pdf')
                ->label('Exportar a PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->url(route('exports.financiero.pdf'))
                ->openUrlInNewTab(),
        ];
    }
}