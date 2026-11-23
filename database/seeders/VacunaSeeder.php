<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vacuna;
use App\Models\Especie;

class VacunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de especies
        $perroId = Especie::where('nombre_especie', 'Perro')->first()->id ?? null;
        $gatoId = Especie::where('nombre_especie', 'Gato')->first()->id ?? null;

        // Verificar que existan las especies
        if (!$perroId || !$gatoId) {
            // Crear especies si no existen
            if (!$perroId) {
                $perro = Especie::create(['nombre_especie' => 'Perro']);
                $perroId = $perro->id;
            }
            
            if (!$gatoId) {
                $gato = Especie::create(['nombre_especie' => 'Gato']);
                $gatoId = $gato->id;
            }
        }

        // Vacunas para perros
        $vacunasPerro = [
            ['nombre' => 'Vacuna Primera', 'tipo' => 'primera', 'especie_id' => $perroId],
            ['nombre' => 'Vacuna Segunda', 'tipo' => 'segunda', 'especie_id' => $perroId],
            ['nombre' => 'Vacuna Tercera', 'tipo' => 'tercera', 'especie_id' => $perroId],
            ['nombre' => 'Vacuna Cuarta', 'tipo' => 'cuarta', 'especie_id' => $perroId],
            ['nombre' => 'Vacuna Quinta', 'tipo' => 'quinta', 'especie_id' => $perroId],
            ['nombre' => 'Vacuna Anual', 'tipo' => 'anual', 'especie_id' => $perroId],
        ];

        // Vacunas para gatos
        $vacunasGato = [
            ['nombre' => 'Vacuna Primera', 'tipo' => 'primera', 'especie_id' => $gatoId],
            ['nombre' => 'Vacuna Segunda', 'tipo' => 'segunda', 'especie_id' => $gatoId],
            ['nombre' => 'Vacuna Tercera', 'tipo' => 'tercera', 'especie_id' => $gatoId],
            ['nombre' => 'Vacuna Cuarta', 'tipo' => 'cuarta', 'especie_id' => $gatoId],
            ['nombre' => 'Vacuna Anual', 'tipo' => 'anual', 'especie_id' => $gatoId],
        ];

        // Insertar todas las vacunas
        foreach (array_merge($vacunasPerro, $vacunasGato) as $vacuna) {
            Vacuna::updateOrCreate(
                ['nombre' => $vacuna['nombre'], 'tipo' => $vacuna['tipo'], 'especie_id' => $vacuna['especie_id']],
                $vacuna
            );
        }
    }
}
