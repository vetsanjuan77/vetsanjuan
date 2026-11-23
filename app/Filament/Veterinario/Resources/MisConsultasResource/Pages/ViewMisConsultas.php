<?php

namespace App\Filament\Veterinario\Resources\MisConsultasResource\Pages;

use App\Filament\Veterinario\Resources\MisConsultasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMisConsultas extends ViewRecord
{
    protected static string $resource = MisConsultasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}