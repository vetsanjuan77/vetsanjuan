<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run()
    {
        $proveedores = [
            [
                'nombre' => 'Distribuidora Pet Food S.A.S',
                'nit' => '900123456-1',
                'telefono' => '(604) 444-5555',
                'email' => 'ventas@petfood.com.co',
                'direccion' => 'Carrera 50 # 25-30',
                'ciudad' => 'Medellín',
                'contacto_nombre' => 'María González',
                'contacto_telefono' => '(604) 444-5556',
                'contacto_email' => 'maria.gonzalez@petfood.com.co',
                'tipo' => 'productos',
                'activo' => true,
                'observaciones' => 'Proveedor principal de alimentos para mascotas'
            ],
            [
                'nombre' => 'Laboratorios Veterinarios Ltda',
                'nit' => '800987654-2',
                'telefono' => '(601) 333-7777',
                'email' => 'pedidos@labvet.com.co',
                'direccion' => 'Calle 72 # 15-45',
                'ciudad' => 'Bogotá',
                'contacto_nombre' => 'Dr. Carlos Ramírez',
                'contacto_telefono' => '(601) 333-7778',
                'contacto_email' => 'carlos.ramirez@labvet.com.co',
                'tipo' => 'productos',
                'activo' => true,
                'observaciones' => 'Especialistas en medicamentos veterinarios'
            ],
            [
                'nombre' => 'Accesorios y Juguetes Pet',
                'nit' => '700456789-3',
                'telefono' => '(605) 222-8888',
                'email' => 'info@accesoriopet.com',
                'direccion' => 'Avenida 30 # 80-12',
                'ciudad' => 'Barranquilla',
                'contacto_nombre' => 'Ana Martínez',
                'contacto_telefono' => '(605) 222-8889',
                'contacto_email' => 'ana.martinez@accesoriopet.com',
                'tipo' => 'productos',
                'activo' => true,
                'observaciones' => 'Amplio catálogo de accesorios y juguetes'
            ],
            [
                'nombre' => 'Suministros Veterinarios del Valle',
                'nit' => '600789123-4',
                'telefono' => '(602) 555-9999',
                'email' => 'compras@sumvet.com.co',
                'direccion' => 'Carrera 25 # 40-60',
                'ciudad' => 'Cali',
                'contacto_nombre' => 'Luis Herrera',
                'contacto_telefono' => '(602) 555-9990',
                'contacto_email' => 'luis.herrera@sumvet.com.co',
                'tipo' => 'ambos',
                'activo' => true,
                'observaciones' => 'Equipos médicos y suministros veterinarios'
            ],
            [
                'nombre' => 'Distribuciones Agropecuarias S.A',
                'nit' => '500321654-5',
                'telefono' => '(607) 111-2222',
                'email' => 'ventas@agropecuarias.com',
                'direccion' => 'Calle 45 # 20-35',
                'ciudad' => 'Bucaramanga',
                'contacto_nombre' => 'Pedro Sánchez',
                'contacto_telefono' => '(607) 111-2223',
                'contacto_email' => 'pedro.sanchez@agropecuarias.com',
                'tipo' => 'productos',
                'activo' => false,
                'observaciones' => 'Proveedor temporal - verificar condiciones'
            ]
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::firstOrCreate(
                ['nit' => $proveedor['nit']], // Condición de búsqueda
                $proveedor // Datos a crear si no existe
            );
        }
    }
}