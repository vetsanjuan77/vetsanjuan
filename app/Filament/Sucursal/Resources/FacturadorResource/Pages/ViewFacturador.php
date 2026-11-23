<?php

namespace App\Filament\Sucursal\Resources\FacturadorResource\Pages;

use App\Filament\Sucursal\Resources\FacturadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFacturador extends ViewRecord
{
    protected static string $resource = FacturadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}