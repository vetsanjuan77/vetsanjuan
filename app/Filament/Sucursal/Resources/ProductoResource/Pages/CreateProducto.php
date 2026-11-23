<?php

namespace App\Filament\Sucursal\Resources\ProductoResource\Pages;

use App\Filament\Sucursal\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected array $stockInicialPresentaciones = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extraer el stock inicial de las presentaciones antes de crear el producto
        if (isset($data['presentaciones'])) {
            foreach ($data['presentaciones'] as $index => $presentacion) {
                // Guardar el stock inicial temporalmente
                $this->stockInicialPresentaciones[$index] = $presentacion['stock_inicial'] ?? 0;
                // Remover el stock_inicial del array de presentaciones ya que no existe en la tabla
                unset($data['presentaciones'][$index]['stock_inicial']);
            }
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $sucursalId = Auth::user()->sucursal_id;
        
        // Crear las relaciones en presentacion_sucursal con el stock inicial
        foreach ($this->record->presentaciones as $index => $presentacion) {
            $stockInicial = $this->stockInicialPresentaciones[$index] ?? 0;
            
            $presentacion->sucursales()->attach($sucursalId, [
                'stock' => $stockInicial,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
