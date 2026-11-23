<?php

namespace App\Filament\Sucursal\Resources\CompraResource\Pages;

use App\Filament\Sucursal\Resources\CompraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompra extends EditRecord
{
    protected static string $resource = CompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
