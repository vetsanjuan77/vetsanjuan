<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'veterinariasanjuan77@gmail.com'],
            [
                'name' => 'Administrador',
                'email' => 'veterinariasanjuan77@gmail.com',
                'password' => Hash::make('Millonario77.'),
                'role' => 'admin',
                'estado' => 'activo'
            ]
        );
    }
}