<?php

namespace App\Filament\Resources\VeterinariosResource\Pages;

use App\Filament\Resources\VeterinariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVeterinarios extends EditRecord
{
    protected static string $resource = VeterinariosResource::class;

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