<?php

namespace App\Filament\Resources\VeterinariosResource\Pages;

use App\Filament\Resources\VeterinariosResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVeterinarios extends ViewRecord
{
    protected static string $resource = VeterinariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}