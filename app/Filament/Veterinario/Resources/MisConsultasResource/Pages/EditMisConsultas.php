<?php

namespace App\Filament\Veterinario\Resources\MisConsultasResource\Pages;

use App\Filament\Veterinario\Resources\MisConsultasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisConsultas extends EditRecord
{
    protected static string $resource = MisConsultasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}