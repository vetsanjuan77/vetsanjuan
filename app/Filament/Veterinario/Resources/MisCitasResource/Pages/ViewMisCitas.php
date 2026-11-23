<?php

namespace App\Filament\Veterinario\Resources\MisCitasResource\Pages;

use App\Filament\Veterinario\Resources\MisCitasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMisCitas extends ViewRecord
{
    protected static string $resource = MisCitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}