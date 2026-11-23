<?php

namespace App\Filament\Sucursal\Resources\ProductoResource\Pages;

use App\Filament\Sucursal\Resources\ProductoResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    /**
     * Datos preparados de actualizaciones de pivot por presentación.
     * [presentacion_id => [stock, precio_venta, ...]]
     */
    protected array $pivotUpdates = [];

    protected function getHeaderActions(): array
    {
        return [
            // Removemos la acción de eliminar ya que los productos se gestionan desde admin
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Preparar actualizaciones de pivot y evitar dehidratar presentaciones al modelo
        $sucursalId = auth()->user()->sucursal_id;

        if (!$sucursalId) {
            throw new \RuntimeException('No se encontró la sucursal del usuario.');
        }

        if (isset($data['presentaciones'])) {
            foreach ($data['presentaciones'] as $presentacionData) {
                $presentacionId = $presentacionData['id'] ?? null;
                if (!$presentacionId) {
                    \Illuminate\Support\Facades\Log::warning('Presentación sin id en datos del formulario', [
                        'producto_id' => $this->record->id,
                        'presentacion' => $presentacionData,
                    ]);
                    continue;
                }
                $presentacion = $this->record->presentaciones()->find($presentacionId);
                if (!$presentacion) {
                    \Illuminate\Support\Facades\Log::warning('Presentación no encontrada al preparar guardado', [
                        'producto_id' => $this->record->id,
                        'presentacion_id' => $presentacionId,
                    ]);
                    continue;
                }
                $this->pivotUpdates[$presentacionId] = [
                    'sucursal_id' => $sucursalId,
                    'stock' => (int) ($presentacionData['stock_sucursal'] ?? 0),
                    'precio_venta' => isset($presentacionData['precio_venta']) ? (float) $presentacionData['precio_venta'] : (float) $presentacion->precio_unitario,
                    'precio_compra' => (float) ($presentacion->precio_unitario * 0.75),
                    'stock_minimo' => 5,
                    'stock_maximo' => (int) (($presentacionData['stock_sucursal'] ?? 0) * 2),
                    'activo' => true,
                ];
            }
        }

        unset($data['presentaciones']);
        return $data;
    }

    protected function afterSave(): void
    {
        if (empty($this->pivotUpdates)) {
            return; // Nada que actualizar
        }

        DB::transaction(function () {
            foreach ($this->pivotUpdates as $presentacionId => $update) {
                $presentacion = $this->record->presentaciones()->find($presentacionId);
                if (! $presentacion) {
                    Log::warning('Presentación no encontrada al guardar stock', [
                        'producto_id' => $this->record->id,
                        'presentacion_id' => $presentacionId,
                    ]);
                    continue;
                }

                $sucursalId = $update['sucursal_id'];

                // Leer valores actuales antes
                $pivotActual = $presentacion->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot;
                Log::info('Stock sucursal antes de actualizar', [
                    'producto_id' => $this->record->id,
                    'presentacion_id' => $presentacionId,
                    'sucursal_id' => $sucursalId,
                    'antes' => $pivotActual ? [
                        'stock' => (int) $pivotActual->stock,
                        'precio_venta' => (float) $pivotActual->precio_venta,
                        'stock_minimo' => (int) $pivotActual->stock_minimo,
                        'stock_maximo' => (int) $pivotActual->stock_maximo,
                        'activo' => (bool) $pivotActual->activo,
                    ] : null,
                    'nuevo' => $update,
                ]);

                // Actualizar/crear la relación
                $presentacion->sucursales()->syncWithoutDetaching([
                    $sucursalId => [
                        'stock' => $update['stock'],
                        'precio_venta' => $update['precio_venta'],
                        'precio_compra' => $update['precio_compra'],
                        'stock_minimo' => $update['stock_minimo'],
                        'stock_maximo' => $update['stock_maximo'],
                        'activo' => $update['activo'],
                    ],
                ]);

                // Verificar persistencia
                $pivotNuevo = $presentacion->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot;
                $persistio = $pivotNuevo
                    && (int) $pivotNuevo->stock === (int) $update['stock']
                    && (float) $pivotNuevo->precio_venta === (float) $update['precio_venta'];

                if (! $persistio) {
                    Log::error('Fallo al persistir stock en presentacion_sucursal', [
                        'producto_id' => $this->record->id,
                        'presentacion_id' => $presentacionId,
                        'sucursal_id' => $sucursalId,
                        'esperado' => $update,
                        'obtenido' => $pivotNuevo ? [
                            'stock' => (int) $pivotNuevo->stock,
                            'precio_venta' => (float) $pivotNuevo->precio_venta,
                        ] : null,
                    ]);
                    throw new \RuntimeException('No se pudo confirmar la actualización del stock en la base de datos.');
                }

                Log::info('Stock sucursal actualizado correctamente', [
                    'producto_id' => $this->record->id,
                    'presentacion_id' => $presentacionId,
                    'sucursal_id' => $sucursalId,
                    'confirmado' => [
                        'stock' => (int) $pivotNuevo->stock,
                        'precio_venta' => (float) $pivotNuevo->precio_venta,
                    ],
                ]);
            }
        });
    }
}
