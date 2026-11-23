<?php

namespace App\Filament\Facturador\Resources\MovimientoCajaResource\Pages;

use App\Filament\Facturador\Resources\MovimientoCajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMovimientoCaja extends ViewRecord
{
    protected static string $resource = MovimientoCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}