<?php

namespace App\Filament\Sucursal\Resources\MovimientoCajaResource\Pages;

use App\Filament\Sucursal\Resources\MovimientoCajaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovimientoCaja extends EditRecord
{
    protected static string $resource = MovimientoCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
