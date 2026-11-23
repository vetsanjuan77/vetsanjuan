<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposPelo = [
            [
                'nombre_tipo_pelo' => 'Corto',
                'descripcion' => 'Pelo corto y liso',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_pelo' => 'Largo',
                'descripcion' => 'Pelo largo y abundante',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_tipo_pelo' => 'Sin Pelo',
                'descripcion' => 'Razas sin pelo o con muy poco pelo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipos_pelo')->insert($tiposPelo);
    }
}
