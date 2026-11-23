<?php

namespace App\Filament\Sucursal\Resources\FacturaResource\Pages;

use App\Filament\Sucursal\Resources\FacturaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $detalles = $data['detalles'] ?? [];
        $subtotal = 0;
        $descuentoTotal = 0;
        foreach ($detalles as $item) {
            $cantidad = (float) ($item['cantidad'] ?? 1);
            $precio = (float) ($item['precio_unitario'] ?? 0);
            $descuento = (float) ($item['descuento'] ?? 0);
            $lineSubtotal = round($cantidad * $precio, 4);
            $subtotal += $lineSubtotal;
            $descuentoTotal += $descuento;
        }
        $impuestos = 0.0; // Se puede calcular según configuración tributaria de la sucursal
        $total = max(0, round($subtotal + $impuestos - $descuentoTotal, 2));

        $data['subtotal'] = $subtotal;
        $data['impuestos'] = $impuestos;
        $data['descuento'] = $descuentoTotal;
        $data['total'] = $total;
        $data['estado'] = $data['estado'] ?? 'pendiente';
        $data['fecha_emision'] = $data['fecha_emision'] ?? now();
        $data['sucursal_id'] = auth()->user()->sucursal_id ?? null;

        // Establecer método de pago por defecto si no se seleccionó
        if (!isset($data['metodo_pago'])) {
            $data['metodo_pago'] = 'efectivo';
        }

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
                            // El cálculo del cambio se hará al guardar también
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
                    // Calcular cambio usando el estado actual del formulario
                    $total = (float) ($this->form->getState()['total'] ?? 0);
                    $recibido = (float) ($data['monto_recibido'] ?? 0);
                    $cambio = max(0, $recibido - $total);

                    // Guardar la factura
                    $this->create();

                    $record = $this->record;
                    if ($record) {
                        // Se podría guardar el cambio como nota si se requiere
                        // Redirigir a recibo si se seleccionó imprimir
                        if (!isset($data['imprimir']) || $data['imprimir'] === true) {
                            $this->redirect(route('facturas.recibo', ['factura' => $record->id]));
                            return;
                        }
                        Notification::make()
                            ->title('Pago registrado')
                            ->body('Cambio: $' . number_format($cambio, 2))
                            ->success()
                            ->send();
                        $this->redirect(static::getResource()::getUrl('index'));
                    } else {
                        Notification::make()
                            ->title('No se pudo completar el pago')
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
