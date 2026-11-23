<?php

namespace App\Filament\Sucursal\Resources\FacturadorResource\Pages;

use App\Filament\Sucursal\Resources\FacturadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacturadors extends ListRecords
{
    protected static string $resource = FacturadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Facturador')
                ->icon('heroicon-o-plus'),
        ];
    }
}