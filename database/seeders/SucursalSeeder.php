<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear sucursales
        $sucursalPoblado = Sucursal::firstOrCreate(
            ['codigo' => 'POB001'],
            [
                'nombre' => 'Punto del Poblado',
                'direccion' => 'Carrera 43A #5-15, El Poblado',
                'telefono' => '604-555-0101',
                'email' => 'poblado@vetsanjuan.com',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'nit' => '900123456-1',
                'activa' => true,
                'configuracion' => [
                    'horario_apertura' => '08:00',
                    'horario_cierre' => '18:00',
                    'dias_laborales' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'],
                    'moneda' => 'COP',
                    'impuesto_defecto' => 19,
                ]
            ]
        );

        $sucursalPlaza = Sucursal::firstOrCreate(
            ['codigo' => 'PLZ001'],
            [
                'nombre' => 'Punto de la Plaza',
                'direccion' => 'Calle 50 #46-36, Centro',
                'telefono' => '604-555-0102',
                'email' => 'plaza@vetsanjuan.com',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'nit' => '900123456-2',
                'activa' => true,
                'configuracion' => [
                    'horario_apertura' => '09:00',
                    'horario_cierre' => '19:00',
                    'dias_laborales' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'],
                    'moneda' => 'COP',
                    'impuesto_defecto' => 19,
                ]
            ]
        );

        // Crear usuarios para cada sucursal
        User::updateOrCreate(
            ['email' => 'admin.poblado@vetsanjuan.com'],
            [
                'name' => 'Administrador Poblado',
                'password' => Hash::make('password123'),
                'role' => 'admin_sucursal',
                'estado' => 'activo',
                'sucursal_id' => $sucursalPoblado->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.plaza@vetsanjuan.com'],
            [
                'name' => 'Administrador Plaza',
                'password' => Hash::make('password123'),
                'role' => 'admin_sucursal',
                'estado' => 'activo',
                'sucursal_id' => $sucursalPlaza->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'vendedor.poblado@vetsanjuan.com'],
            [
                'name' => 'Vendedor Poblado',
                'password' => Hash::make('password123'),
                'role' => 'vendedor',
                'estado' => 'activo',
                'sucursal_id' => $sucursalPoblado->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'vendedor.plaza@vetsanjuan.com'],
            [
                'name' => 'Vendedor Plaza',
                'password' => Hash::make('password123'),
                'role' => 'vendedor',
                'estado' => 'activo',
                'sucursal_id' => $sucursalPlaza->id,
            ]
        );

        // Crear cajas para cada sucursal
        \App\Models\Caja::firstOrCreate(
            ['sucursal_id' => $sucursalPoblado->id, 'estado' => 'abierta'],
            [
                'nombre' => 'Caja Principal Poblado',
                'user_id' => 1,
                'fecha_apertura' => now(),
                'saldo_inicial' => 500000.00,
                'observaciones' => 'Caja principal - Apertura inicial',
            ]
        );

        \App\Models\Caja::firstOrCreate(
            ['sucursal_id' => $sucursalPlaza->id, 'estado' => 'abierta'],
            [
                'nombre' => 'Caja Principal Plaza',
                'user_id' => 2,
                'fecha_apertura' => now(),
                'saldo_inicial' => 300000.00,
                'observaciones' => 'Caja principal - Apertura inicial',
            ]
        );
    }
}
