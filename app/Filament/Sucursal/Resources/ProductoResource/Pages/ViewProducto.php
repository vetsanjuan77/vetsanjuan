<?php

namespace App\Filament\Sucursal\Resources\ProductoResource\Pages;

use App\Filament\Sucursal\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProducto extends ViewRecord
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}