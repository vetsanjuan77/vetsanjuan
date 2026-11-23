<?php

namespace App\Filament\Facturador\Resources\FacturaResource\Pages;

use App\Filament\Facturador\Resources\FacturaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacturas extends ListRecords
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        $this->redirect(FacturaResource::getUrl('create'));
    }
}