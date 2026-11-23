<?php

namespace App\Filament\Sucursal\Resources\ReporteResource\Pages;

use App\Filament\Sucursal\Resources\ReporteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteInventarioExport;

class ReporteInventario extends Page
{
    protected static string $resource = ReporteResource::class;

    protected static string $view = 'filament.sucursal.resources.reporte-resource.pages.reporte-inventario';

    protected static ?string $title = 'Reportes de Inventario';
    
    protected static ?string $navigationLabel = 'Inventario';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\InventarioStockWidget::class,
            \App\Filament\Sucursal\Resources\ReporteResource\Widgets\MovimientosInventarioWidget::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('exportar_excel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('exports.inventario.excel'))
                ->openUrlInNewTab(),
            
            Actions\Action::make('exportar_pdf')
                ->label('Exportar a PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->url(route('exports.inventario.pdf'))
                ->openUrlInNewTab(),
        ];
    }
}