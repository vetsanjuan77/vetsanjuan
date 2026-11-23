<?php

namespace App\Filament\Veterinario\Resources\RecetasResource\Pages;

use App\Filament\Veterinario\Resources\RecetasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecetas extends CreateRecord
{
    protected static string $resource = RecetasResource::class;

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