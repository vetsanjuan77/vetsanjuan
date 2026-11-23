<?php

namespace App\Filament\Sucursal\Resources\FacturadorResource\Pages;

use App\Filament\Sucursal\Resources\FacturadorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditFacturador extends EditRecord
{
    protected static string $resource = FacturadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // No mostrar la contraseña en el formulario de edición
        unset($data['password']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mantener la sucursal original (no permitir cambios)
        $data['sucursal_id'] = $this->record->sucursal_id;
        
        // Mantener el rol de facturador
        $data['role'] = 'facturador';
        
        // Si se cambió la contraseña, actualizar la fecha
        if (!empty($data['password'])) {
            $data['password_changed_at'] = now();
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Facturador actualizado')
            ->body('Los datos del facturador han sido actualizados correctamente.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}