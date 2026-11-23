<?php

namespace App\Filament\Sucursal\Resources\ProductoResource\Pages;

use App\Filament\Sucursal\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
