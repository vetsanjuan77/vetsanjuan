<?php

namespace App\Filament\Peluquero\Resources\CitaPeluqueriaResource\Pages;

use App\Filament\Peluquero\Resources\CitaPeluqueriaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCitaPeluqueria extends CreateRecord
{
    protected static string $resource = CitaPeluqueriaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['peluquero_id'] = auth()->id();
        return $data;
    }
}
