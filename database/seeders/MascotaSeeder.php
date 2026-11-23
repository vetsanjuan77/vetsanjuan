<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Mascotas;
use App\Models\Especie;
use App\Models\Sexo;
use App\Models\Tamano;
use App\Models\TipoPelo;
use App\Models\Raza;
use Carbon\Carbon;

class MascotaSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar a la usuaria Laura por email (creada en UserSeeder)
        $user = User::where('email', 'lalalions2006@gmail.com')->first();
        if (!$user) {
            // Si no existe el usuario, salimos para evitar registros huérfanos
            return;
        }

        // Obtener el cliente asociado
        $cliente = Cliente::where('user_id', $user->id)->first();
        if (!$cliente) {
            // Si no existe el cliente, salimos para evitar registros huérfanos
            return;
        }

        // Conseguir catálogos básicos (usar primero disponible)
        $especie = Especie::first();
        if (!$especie) {
            // En caso extremo, crear una especie por defecto
            $especie = Especie::create(['nombre_especie' => 'Perro']);
        }

        // Preferir 'Hembra' si existe, si no, el primero
        $sexo = Sexo::where('description', 'Hembra')->first() ?? Sexo::first();
        $tamano = Tamano::first();
        $tipoPelo = TipoPelo::first();
        $raza = Raza::first();

        // Datos de la mascota
        $fechaNacimiento = Carbon::parse('2021-04-01');
        $edad = $fechaNacimiento->diffInYears(Carbon::now());

        // Crear la mascota de Laura (idempotente)
        Mascotas::firstOrCreate(
            [
                'nombre_mascota' => 'Luna',
                'cliente_id' => $cliente->id,
            ],
            [
                'fecha_nacimiento' => $fechaNacimiento->toDateString(),
                'especie_id' => $especie?->id,
                'sexo_id' => $sexo?->id,
                'tamano_id' => $tamano?->id,
                'tipo_pelo_id' => $tipoPelo?->id,
                'raza_id' => $raza?->id,
                'edad' => $edad ?: 3,
                'peso' => 12.5,
                'alergias' => null,
                'comentarios' => 'Mascota de Laura creada por seeder.',
                'foto' => null,
            ]
        );
    }
}