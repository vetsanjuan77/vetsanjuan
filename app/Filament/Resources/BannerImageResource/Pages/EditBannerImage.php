<?php

namespace App\Filament\Resources\BannerImageResource\Pages;

use App\Filament\Resources\BannerImageResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditBannerImage extends EditRecord
{
    protected static string $resource = BannerImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }
}