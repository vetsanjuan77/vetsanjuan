<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use App\Models\Sucursal;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected function afterCreate(): void
    {
        // Al crear un producto desde el panel de administración,
        // vinculamos automáticamente cada presentación a todas las sucursales activas
        // con stock=0 y precio_venta igual al precio_unitario de la presentación.
        $sucursales = Sucursal::query()->where('activa', true)->get(['id']);

        foreach ($this->record->presentaciones as $presentacion) {
            foreach ($sucursales as $sucursal) {
                // Evitar duplicados en caso de que ya exista la relación
                if (! $presentacion->sucursales()->where('sucursal_id', $sucursal->id)->exists()) {
                    $presentacion->sucursales()->attach($sucursal->id, [
                        'stock' => 0,
                        'stock_minimo' => 0,
                        'stock_maximo' => 0,
                        'precio_venta' => $presentacion->precio_unitario,
                        'precio_compra' => null,
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
