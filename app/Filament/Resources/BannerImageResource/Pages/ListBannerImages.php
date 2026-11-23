<?php

namespace App\Filament\Resources\BannerImageResource\Pages;

use App\Filament\Resources\BannerImageResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListBannerImages extends ListRecords
{
    protected static string $resource = BannerImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Añadir imagen'),
        ];
    }
}