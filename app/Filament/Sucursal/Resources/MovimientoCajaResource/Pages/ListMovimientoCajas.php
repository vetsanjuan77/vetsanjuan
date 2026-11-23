<?php

namespace App\Filament\Sucursal\Resources\MovimientoCajaResource\Pages;

use App\Filament\Sucursal\Resources\MovimientoCajaResource;
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
