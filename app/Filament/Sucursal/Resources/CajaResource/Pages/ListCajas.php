<?php

namespace App\Filament\Sucursal\Resources\CajaResource\Pages;

use App\Filament\Sucursal\Resources\CajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCajas extends ListRecords
{
    protected static string $resource = CajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
