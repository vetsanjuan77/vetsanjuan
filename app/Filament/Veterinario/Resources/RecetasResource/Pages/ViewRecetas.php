<?php

namespace App\Filament\Veterinario\Resources\RecetasResource\Pages;

use App\Filament\Veterinario\Resources\RecetasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRecetas extends ViewRecord
{
    protected static string $resource = RecetasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}