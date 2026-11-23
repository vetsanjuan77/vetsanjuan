<?php

namespace App\Filament\Resources\VeterinariosResource\Pages;

use App\Filament\Resources\VeterinariosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VeterinarioCredenciales;

class CreateVeterinarios extends CreateRecord
{
    protected static string $resource = VeterinariosResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si no se proporcionó contraseña, generar una automáticamente
        if (empty($data['password'])) {
            $passwordGenerada = Str::random(8);
            $data['password'] = Hash::make($passwordGenerada);
            
            // Guardar la contraseña generada para mostrarla al usuario
            session(['password_generada' => $passwordGenerada]);
        }
        
        // Asegurar que el rol sea veterinario
        $data['role'] = 'veterinario';
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $passwordGenerada = session('password_generada');
        
        if ($passwordGenerada) {
            // Enviar credenciales por email
            try {
                Mail::to($this->record->email)->send(new VeterinarioCredenciales($this->record, $passwordGenerada));
                
                Notification::make()
                    ->title('Veterinario creado exitosamente')
                    ->body("Se han enviado las credenciales a {$this->record->email}")
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Veterinario creado')
                    ->body("Veterinario creado pero no se pudo enviar el email. Credenciales: {$this->record->email} / {$passwordGenerada}")
                    ->warning()
                    ->send();
            }
            
            // Limpiar la sesión
            session()->forget('password_generada');
        }
    }
}