<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\Cliente;
use App\Models\Sucursal;
use App\Models\Producto;
use App\Models\Presentacion;
use Carbon\Carbon;

class VentasSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Asegurar una sucursal existente
            $sucursal = Sucursal::first();
            if (! $sucursal) {
                $sucursal = Sucursal::create([
                    'nombre' => 'Sucursal Central',
                    'codigo' => 'CENTRAL-' . Str::upper(Str::random(4)),
                    'direccion' => 'Calle 123 #45-67',
                    'telefono' => '3001234567',
                    'email' => 'central' . Str::random(4) . '@empresa.com',
                    'ciudad' => 'Medellín',
                    'departamento' => 'Antioquia',
                    'activa' => true,
                ]);
            }

            // Asegurar clientes
            $clientes = Cliente::inRandomOrder()->take(5)->get();
            if ($clientes->count() < 5) {
                // Obtener ciudad y usuario válidos
                $ciudadId = DB::table('ciudades')->inRandomOrder()->value('id');
                if (! $ciudadId) {
                    // En caso extremo, crear departamento y ciudad mínimas
                    $departamentoId = DB::table('departamentos')->insertGetId(['nombre' => 'Antioquia', 'created_at' => now(), 'updated_at' => now()]);
                    $ciudadId = DB::table('ciudades')->insertGetId(['nombre' => 'Medellín', 'departamento_id' => $departamentoId, 'created_at' => now(), 'updated_at' => now()]);
                }
                $userCliente = DB::table('users')->where('role', 'cliente')->inRandomOrder()->first();
                if (! $userCliente) {
                    $userId = DB::table('users')->insertGetId([
                        'name' => 'Cliente Usuario',
                        'email' => 'cliente' . Str::random(6) . '@correo.com',
                        'password' => Hash::make('password'),
                        'role' => 'cliente',
                        'estado' => 'activo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $userId = $userCliente->id;
                }

                for ($i = $clientes->count(); $i < 5; $i++) {
                    $clientes->push(Cliente::create([
                        'user_id' => $userId,
                        'nombre' => 'Cliente ' . Str::random(5),
                        'apellido' => Str::upper(Str::random(5)),
                        'email' => 'cliente' . Str::random(5) . '@correo.com',
                        'telefono' => '31' . rand(10000000, 99999999),
                        'direccion' => 'Calle ' . rand(1, 100) . ' # ' . rand(1, 99),
                        'ciudad_id' => $ciudadId,
                    ]));
                }
            }

            // Asegurar productos y presentaciones
            $productos = Producto::inRandomOrder()->take(8)->get();
            if ($productos->isEmpty()) {
                // Asegurar una marca
                $marcaId = DB::table('marcas')->inRandomOrder()->value('id');
                if (! $marcaId) {
                    $marcaId = DB::table('marcas')->insertGetId(['nombre' => 'Genérica', 'created_at' => now(), 'updated_at' => now()]);
                }
                for ($i = 0; $i < 8; $i++) {
                    $producto = Producto::create([
                        'nombre' => 'Producto ' . Str::random(6),
                        'precio' => rand(10000, 120000),
                        'description' => 'Producto de prueba',
                        'especie' => 'canino',
                        'marca_id' => $marcaId,
                        'categoria' => 'alimento',
                        'edad' => 'adulto',
                        'cantidad_inventario' => rand(10, 200),
                    ]);
                    $presentacion = Presentacion::create([
                        'producto_id' => $producto->id,
                        'nombre' => 'Presentación ' . Str::random(4),
                        'precio_unitario' => $producto->precio,
                        'cantidad_inventario' => rand(10, 100),
                    ]);
                    $productos->push($producto);
                }
            }

            // Generar facturas presenciales (4)
            for ($i = 0; $i < 4; $i++) {
                $cliente = $clientes->random();
                $fecha = Carbon::now()->subDays(rand(0, 10));
                $factura = Factura::create([
                    'cliente_id' => $cliente->id,
                    'sucursal_id' => $sucursal->id,
                    'fecha_emision' => $fecha,
                    'subtotal' => 0,
                    'impuestos' => 0,
                    'descuento' => 0,
                    'total' => 0,
                    'estado' => 'pagada',
                    'metodo_pago' => 'efectivo',
                    'tipo_venta' => 'presencial',
                ]);

                $lineas = rand(1, 3);
                $subtotal = 0;
                for ($l = 0; $l < $lineas; $l++) {
                    $producto = $productos->random();
                    $cantidad = rand(1, 3);
                    $precio = $producto->precio;
                    $lineaSubtotal = $cantidad * $precio;

                    DetalleFactura::create([
                        'factura_id' => $factura->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $lineaSubtotal,
                        'descuento' => 0,
                        'total' => $lineaSubtotal,
                    ]);
                    $subtotal += $lineaSubtotal;
                }

                $factura->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'fecha_pago' => $fecha,
                ]);
            }

            // Generar facturas virtuales (4) con datos de envío
            for ($i = 0; $i < 4; $i++) {
                $cliente = $clientes->random();
                $fecha = Carbon::now()->subDays(rand(0, 10));
                $factura = Factura::create([
                    'cliente_id' => $cliente->id,
                    'sucursal_id' => $sucursal->id,
                    'fecha_emision' => $fecha,
                    'subtotal' => 0,
                    'impuestos' => 0,
                    'descuento' => 0,
                    'total' => 0,
                    'estado' => 'pendiente',
                    'metodo_pago' => 'pasarela',
                    'tipo_venta' => 'virtual',
                    'envio_contacto_nombre' => $cliente->nombre,
                    'envio_contacto_telefono' => $cliente->telefono ?? '300' . rand(1000000, 9999999),
                    'envio_departamento' => 'Antioquia',
                    'envio_ciudad' => 'Medellín',
                    'envio_direccion' => 'Cra ' . rand(1, 99) . ' # ' . rand(1, 99) . ' - ' . rand(1, 99),
                    'envio_codigo_postal' => '0500' . rand(10, 99),
                ]);

                $lineas = rand(1, 3);
                $subtotal = 0;
                for ($l = 0; $l < $lineas; $l++) {
                    $producto = $productos->random();
                    $cantidad = rand(1, 2);
                    $precio = $producto->precio;
                    $lineaSubtotal = $cantidad * $precio;

                    DetalleFactura::create([
                        'factura_id' => $factura->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $lineaSubtotal,
                        'descuento' => 0,
                        'total' => $lineaSubtotal,
                    ]);
                    $subtotal += $lineaSubtotal;
                }

                $factura->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ]);
            }
        });
    }
}