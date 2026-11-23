<?php

namespace App\Filament\Sucursal\Resources\CompraResource\Pages;

use App\Filament\Sucursal\Resources\CompraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompra extends ViewRecord
{
    protected static string $resource = CompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('marcar_recibida')
                ->label('Marcar como Recibida')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->estado === 'pendiente')
                ->action(function (): void {
                    $this->record->update([
                        'estado' => 'recibida',
                        'fecha_recepcion' => now(),
                    ]);
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->requiresConfirmation(),
        ];
    }
}