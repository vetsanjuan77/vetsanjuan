<?php

namespace App\Filament\Sucursal\Resources\CompraResource\Pages;

use App\Filament\Sucursal\Resources\CompraResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompra extends CreateRecord
{
    protected static string $resource = CompraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calcular el total automáticamente
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $impuestos = (float) ($data['impuestos'] ?? 0);
        $descuento = (float) ($data['descuento'] ?? 0);
        
        $data['total'] = $subtotal + $impuestos - $descuento;
        
        return $data;
    }
}
