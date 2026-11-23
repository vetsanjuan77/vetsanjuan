<?php

namespace App\Filament\Sucursal\Resources\GastoResource\Pages;

use App\Filament\Sucursal\Resources\GastoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGastos extends ListRecords
{
    protected static string $resource = GastoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
