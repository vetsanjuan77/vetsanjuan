<?php

namespace App\Filament\Sucursal\Resources\CajaResource\Pages;

use App\Filament\Sucursal\Resources\CajaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaja extends EditRecord
{
    protected static string $resource = CajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
