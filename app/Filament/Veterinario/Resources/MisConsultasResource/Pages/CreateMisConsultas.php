<?php

namespace App\Filament\Veterinario\Resources\MisConsultasResource\Pages;

use App\Filament\Veterinario\Resources\MisConsultasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMisConsultas extends CreateRecord
{
    protected static string $resource = MisConsultasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['veterinario_id'] = auth()->id();
        
        return $data;
    }
}