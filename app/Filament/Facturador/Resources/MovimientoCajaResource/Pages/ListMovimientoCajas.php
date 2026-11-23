<?php

namespace App\Filament\Facturador\Resources\MovimientoCajaResource\Pages;

use App\Filament\Facturador\Resources\MovimientoCajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMovimientoCajas extends ListRecords
{
    protected static string $resource = MovimientoCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}