<?php

namespace App\Filament\Sucursal\Resources\FacturadorResource\Pages;

use App\Filament\Sucursal\Resources\FacturadorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\FacturadorCredentials;

class CreateFacturador extends CreateRecord
{
    protected static string $resource = FacturadorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asignar automáticamente la sucursal del usuario autenticado
        $data['sucursal_id'] = auth()->user()->sucursal_id;
        
        // Asignar el rol de facturador
        $data['role'] = 'facturador';
        
        // Si no se proporcionó contraseña, generar una temporal
        if (empty($data['password'])) {
            $temporalPassword = Str::random(12);
            $data['password'] = Hash::make($temporalPassword);
            
            // Guardar la contraseña temporal para mostrarla al usuario
            session(['temporal_password' => $temporalPassword]);
        }
        
        // Establecer fecha de cambio de contraseña
        $data['password_changed_at'] = now();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $temporalPassword = session('temporal_password');
        $panelUrl = url('/facturador');
        
        if ($temporalPassword) {
            // Limpiar la sesión
            session()->forget('temporal_password');
            
            // Enviar email con credenciales
            try {
                Mail::to($this->record->email)->send(
                    new FacturadorCredentials(
                        $this->record,
                        auth()->user()->sucursal,
                        $temporalPassword,
                        $panelUrl
                    )
                );
                
                Notification::make()
                    ->title('Facturador creado exitosamente')
                    ->body("Se han enviado las credenciales por email a: {$this->record->email}\n\nCredenciales:\nEmail: {$this->record->email}\nContraseña temporal: {$temporalPassword}\n\nPanel: {$panelUrl}")
                    ->success()
                    ->persistent()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Facturador creado exitosamente')
                    ->body("⚠️ No se pudo enviar el email automáticamente.\n\nCredenciales generadas:\nEmail: {$this->record->email}\nContraseña temporal: {$temporalPassword}\n\nPanel: {$panelUrl}")
                    ->warning()
                    ->persistent()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Facturador creado exitosamente')
                ->body("El facturador puede acceder con sus credenciales en: {$panelUrl}")
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}