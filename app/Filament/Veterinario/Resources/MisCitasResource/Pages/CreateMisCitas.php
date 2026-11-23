<?php

namespace App\Filament\Veterinario\Resources\MisCitasResource\Pages;

use App\Filament\Veterinario\Resources\MisCitasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMisCitas extends CreateRecord
{
    protected static string $resource = MisCitasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}