<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente: Laura León
        $clienteUser = User::firstOrCreate(
            ['email' => 'lalalions2006@gmail.com'],
            [
                'name' => 'Laura León',
                'password' => Hash::make('JDRCmicielo11'),
                'role' => 'cliente',
                'estado' => 'activo',
            ]
        );

        // Obtener una ciudad válida para el cliente (usa la primera disponible)
        $ciudadId = DB::table('ciudades')->value('id');
        if (!$ciudadId) {
            // Si no existen ciudades todavía, intentamos con el primer departamento existente
            $departamentoId = DB::table('departamentos')->value('id');
            if ($departamentoId) {
                $ciudadId = DB::table('ciudades')->insertGetId([
                    'nombre' => 'Medellín',
                    'departamento_id' => $departamentoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Sin departamentos ni ciudades, salimos temprano para evitar duplicados o claves inválidas
                return;
            }
        }

        // Crear/asegurar el registro de Cliente vinculado al usuario
        Cliente::firstOrCreate(
            ['user_id' => $clienteUser->id],
            [
                'user_id' => $clienteUser->id,
                'nombre' => 'Laura',
                'apellido' => 'León',
                'direccion' => null,
                'telefono' => null,
                'email' => 'lalalions2006@gmail.com',
                'ciudad_id' => $ciudadId,
            ]
        );

        // Peluquero: peluquero 1
        User::firstOrCreate(
            ['email' => 'peluquero1@gmail.com'],
            [
                'name' => 'peluquero 1',
                'password' => Hash::make('holasoyelpeluquero1'),
                'role' => 'peluquero',
                'estado' => 'activo',
            ]
        );

        // Veterinario: veterinario 1
        User::firstOrCreate(
            ['email' => 'veterinario1@gmail.com'],
            [
                'name' => 'veterinario 1',
                'password' => Hash::make('soyelveterinario1'),
                'role' => 'veterinario',
                'estado' => 'activo',
            ]
        );
    }
}