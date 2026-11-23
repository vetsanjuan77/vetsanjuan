<?php

namespace App\Filament\Peluquero\Resources\CitaPeluqueriaResource\Pages;

use App\Filament\Peluquero\Resources\CitaPeluqueriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCitaPeluqueria extends EditRecord
{
    protected static string $resource = CitaPeluqueriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
