<?php

namespace App\Filament\Sucursal\Resources\CompraResource\Pages;

use App\Filament\Sucursal\Resources\CompraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompras extends ListRecords
{
    protected static string $resource = CompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
