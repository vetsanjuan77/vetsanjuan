<?php

namespace App\Filament\Resources\SucursalResource\Pages;

use App\Filament\Resources\SucursalResource;
use App\Models\User;
use App\Mail\SucursalCredentials;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CreateSucursal extends CreateRecord
{
    protected static string $resource = SucursalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extraer datos del administrador
        $adminData = [
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => $data['admin_password'],
        ];

        // Remover datos del administrador del array principal
        unset($data['admin_name'], $data['admin_email'], $data['admin_password']);

        // Guardar datos del administrador para usar después de crear la sucursal
        $this->adminData = $adminData;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Crear el usuario administrador de la sucursal
        $admin = User::create([
            'name' => $this->adminData['name'],
            'email' => $this->adminData['email'],
            'password' => $this->adminData['password'],
            'role' => 'admin_sucursal',
            'estado' => 'activo',
            'sucursal_id' => $this->record->id,
            'password_changed_at' => now(),
        ]);

        // Actualizar la sucursal con el ID del usuario
        $this->record->update(['user_id' => $admin->id]);

        // Enviar credenciales por email
        try {
            // Obtener la contraseña en texto plano (solo para el email)
            $plainPassword = request()->input('data.admin_password');
            
            Mail::to($admin->email)->send(
                new SucursalCredentials($admin, $plainPassword, $this->record)
            );
            
            Notification::make()
                ->title('Sucursal Creada Exitosamente')
                ->body('Se han enviado las credenciales de acceso al administrador por email.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Sucursal Creada')
                ->body('La sucursal fue creada pero no se pudo enviar el email. Verifique la configuración de correo.')
                ->warning()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}