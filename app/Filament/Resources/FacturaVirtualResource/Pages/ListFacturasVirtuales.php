<?php

namespace App\Filament\Resources\FacturaVirtualResource\Pages;

use App\Filament\Resources\FacturaVirtualResource;
use Filament\Resources\Pages\ListRecords;

class ListFacturasVirtuales extends ListRecords
{
    protected static string $resource = FacturaVirtualResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}