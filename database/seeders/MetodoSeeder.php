<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Metodo;

class MetodoSeeder extends Seeder
{
    public function run(): void
    {
        Metodo::firstOrCreate(['descripcion' => 'Mercado Pago']);
    }
}
