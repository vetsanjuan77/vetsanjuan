<?php

namespace App\Filament\Facturador\Resources\FacturaResource\Pages;

use App\Filament\Facturador\Resources\FacturaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Carbon;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calcular totales desde detalles
        $subtotal = 0;
        $descuentoTotal = 0;
        foreach (($data['detalles'] ?? []) as $detalle) {
            $cantidad = (float) ($detalle['cantidad'] ?? 1);
            $precio = (float) ($detalle['precio_unitario'] ?? 0);
            $descuento = (float) ($detalle['descuento'] ?? 0);
            $lineSubtotal = round($cantidad * $precio, 4);
            $subtotal += $lineSubtotal;
            $descuentoTotal += $descuento;
        }
        $impuestos = 0; // Ajustar si aplica
        $total = max(0, round($subtotal + $impuestos - $descuentoTotal, 2));
        $data['subtotal'] = $subtotal;
        $data['descuento'] = $descuentoTotal;
        $data['impuestos'] = $impuestos;
        $data['total'] = $total;

        // Método de pago y estado preliminar
        if (!isset($data['metodo_pago'])) {
            $data['metodo_pago'] = 'efectivo';
        }
        $data['estado'] = 'pendiente';

        // Campos de contexto requeridos por el modelo/DB
        $data['fecha_emision'] = $data['fecha_emision'] ?? now();
        $data['sucursal_id'] = auth()->user()->sucursal_id ?? null;
        $data['tipo_venta'] = $data['tipo_venta'] ?? 'presencial';

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('pagar')
                ->label('Pagar')
                ->modalHeading('Pago')
                ->color('primary')
                ->form([
                    TextInput::make('monto_recibido')
                        ->label('Monto recibido')
                        ->numeric()
                        ->reactive()
                        ->prefix('$')
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('cambio', (float) $state);
                        }),
                    TextInput::make('cambio')
                        ->label('Cambio (se calcula al confirmar)')
                        ->disabled()
                        ->dehydrated(false)
                        ->numeric()
                        ->default(0),
                    Toggle::make('imprimir')
                        ->label('Imprimir/mostrar recibo')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $total = (float) ($this->form->getState()['total'] ?? 0);
                    $recibido = (float) ($data['monto_recibido'] ?? 0);
                    $cambio = max(0, $recibido - $total);

                    // Crear la factura
                    $this->create();

                    // Redirigir al recibo si se solicitó imprimir
                    $record = $this->record;
                    if ($record && ($data['imprimir'] ?? true)) {
                        $this->redirect(route('facturas.recibo', ['factura' => $record->id]));
                    }
                }),
            Actions\CreateAction::make(),
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}