<?php

namespace App\Filament\Facturador\Resources\CajaResource\Pages;

use App\Filament\Facturador\Resources\CajaResource;
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