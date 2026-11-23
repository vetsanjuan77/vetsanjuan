<?php

namespace App\Filament\Sucursal\Resources\FacturaResource\Pages;

use App\Filament\Sucursal\Resources\FacturaResource;
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
        // Redirige automáticamente al formulario de creación para que la facturación salga primero
        $this->redirect(FacturaResource::getUrl('create'));
    }
}
