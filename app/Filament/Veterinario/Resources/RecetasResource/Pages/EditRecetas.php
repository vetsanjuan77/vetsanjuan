<?php

namespace App\Filament\Veterinario\Resources\RecetasResource\Pages;

use App\Filament\Veterinario\Resources\RecetasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecetas extends EditRecord
{
    protected static string $resource = RecetasResource::class;

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