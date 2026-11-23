<?php

namespace App\Filament\Veterinario\Resources\MisCitasResource\Pages;

use App\Filament\Veterinario\Resources\MisCitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisCitas extends EditRecord
{
    protected static string $resource = MisCitasResource::class;

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