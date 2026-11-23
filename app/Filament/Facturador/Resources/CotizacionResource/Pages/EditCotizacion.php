<?php

namespace App\Filament\Facturador\Resources\CotizacionResource\Pages;

use App\Filament\Facturador\Resources\CotizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCotizacion extends EditRecord
{
    protected static string $resource = CotizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}