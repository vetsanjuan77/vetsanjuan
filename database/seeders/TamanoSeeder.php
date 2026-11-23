<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TamanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tamanos = [
            [
                'nombre_tamano' => 'Pequeño',
                'descripcion' => 'Mascotas de 5-15 kg (Beagle, Cocker Spaniel, etc.)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tamano' => 'Mediano',
                'descripcion' => 'Mascotas de 15-30 kg (Border Collie, Bulldog, etc.)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tamano' => 'Grande',
                'descripcion' => 'Mascotas de 30-45 kg (Labrador, Golden Retriever, etc.)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Evita errores por duplicados al re-ejecutar el seeder
        DB::table('tamanos')->insertOrIgnore($tamanos);
    }
}
