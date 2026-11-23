<?php

namespace App\Filament\Facturador\Resources\CajaResource\Pages;

use App\Filament\Facturador\Resources\CajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCaja extends ViewRecord
{
    protected static string $resource = CajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}