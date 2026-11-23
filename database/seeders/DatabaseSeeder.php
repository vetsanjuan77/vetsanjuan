<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cliente;
use App\Models\Departamento;
use App\Models\Servicios;
use App\Models\Sexo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            // Primero datos base de ubicación
            DepartamentosSeeder::class,
            CiudadesSeeder::class,
            // Luego usuarios y sucursales que dependen de ciudades/departamentos
            UserSeeder::class,
            SucursalSeeder::class,
            MarcaSeeder::class,
            ProductoSucursalSeeder::class, // Mover antes de PresentacionSeeder
            PresentacionSeeder::class,
            ServiciosSeeder::class,
            EspecieSeeder::class,
            SexoSeeder::class,
            VacunaSeeder::class,
            TamanoSeeder::class,
            TipoPeloSeeder::class,
            RazaSeeder::class,
            // Mascota de Laura
            MascotaSeeder::class,
            ProveedorSeeder::class,
            CompraSeeder::class,
            MetodoSeeder::class,
            VentasSeeder::class,
        ]);
    }
}
