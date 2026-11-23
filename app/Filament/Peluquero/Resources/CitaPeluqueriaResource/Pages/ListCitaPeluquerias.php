<?php

namespace App\Filament\Peluquero\Resources\CitaPeluqueriaResource\Pages;

use App\Filament\Peluquero\Resources\CitaPeluqueriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCitaPeluquerias extends ListRecords
{
    protected static string $resource = CitaPeluqueriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
