<?php

namespace App\Filament\Resources\SucursalResource\Pages;

use App\Filament\Resources\SucursalResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EditSucursal extends EditRecord
{
    protected static string $resource = SucursalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar datos del administrador si existe
        if ($this->record->usuario) {
            $data['admin_name'] = $this->record->usuario->name;
            $data['admin_email'] = $this->record->usuario->email;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si hay datos del administrador, actualizarlos
        if (isset($data['admin_name']) || isset($data['admin_email']) || isset($data['admin_password'])) {
            $adminData = [];
            
            if (isset($data['admin_name'])) {
                $adminData['name'] = $data['admin_name'];
                unset($data['admin_name']);
            }
            
            if (isset($data['admin_email'])) {
                $adminData['email'] = $data['admin_email'];
                unset($data['admin_email']);
            }
            
            if (isset($data['admin_password']) && !empty($data['admin_password'])) {
                $adminData['password'] = $data['admin_password'];
                $adminData['password_changed_at'] = now();
                unset($data['admin_password']);
            }

            // Guardar datos del administrador para usar después de actualizar
            $this->adminData = $adminData;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Actualizar datos del administrador si existen
        if (isset($this->adminData) && !empty($this->adminData)) {
            if ($this->record->usuario) {
                $this->record->usuario->update($this->adminData);
                
                Notification::make()
                    ->title('Datos Actualizados')
                    ->body('Los datos del administrador han sido actualizados correctamente.')
                    ->success()
                    ->send();
            } else {
                // Si no existe administrador, crear uno nuevo
                $admin = User::create([
                    'name' => $this->adminData['name'] ?? 'Administrador',
                    'email' => $this->adminData['email'],
                    'password' => $this->adminData['password'] ?? Hash::make('password123'),
                    'role' => 'admin_sucursal',
                    'estado' => 'activo',
                    'sucursal_id' => $this->record->id,
                    'password_changed_at' => now(),
                ]);

                $this->record->update(['user_id' => $admin->id]);
                
                Notification::make()
                    ->title('Administrador Creado')
                    ->body('Se ha creado un nuevo administrador para esta sucursal.')
                    ->success()
                    ->send();
            }
        }
    }
}