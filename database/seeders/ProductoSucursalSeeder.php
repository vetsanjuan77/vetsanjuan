<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Presentacion;
use App\Models\Sucursal;
use App\Models\Marca;

class ProductoSucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener sucursales
        $sucursalPoblado = Sucursal::where('nombre', 'Punto del Poblado')->first();
        $sucursalPlaza = Sucursal::where('nombre', 'Punto de la Plaza')->first();

        // Crear marcas si no existen
        $marcas = [
            'Royal Canin',
            'Hills',
            'Purina',
            'Bayer',
            'Zoetis',
            'Virbac'
        ];

        foreach ($marcas as $nombreMarca) {
            Marca::firstOrCreate(['nombre' => $nombreMarca]);
        }

        // Obtener marcas creadas
        $royalCanin = Marca::where('nombre', 'Royal Canin')->first();
        $hills = Marca::where('nombre', 'Hills')->first();
        $purina = Marca::where('nombre', 'Purina')->first();
        $bayer = Marca::where('nombre', 'Bayer')->first();

        // Crear productos con presentaciones
        $productos = [
            [
                'nombre' => 'Royal Canin Adult Perro',
                'description' => 'Alimento completo para perros adultos de razas medianas y grandes',
                'especie' => 'perro',
                'marca_id' => $royalCanin->id,
                'edad' => 'adulto',
                'categoria' => 'Alimentos',
                'precio' => 185000.00,
                'destacado' => true,
                'presentaciones' => [
                    [
                        'nombre' => '15kg',
                        'precio_unitario' => 185000.00,
                        'stock_poblado' => 25,
                        'stock_plaza' => 15,
                    ],
                    [
                        'nombre' => '7.5kg',
                        'precio_unitario' => 95000.00,
                        'stock_poblado' => 40,
                        'stock_plaza' => 30,
                    ]
                ]
            ],
            [
                'nombre' => 'Hills Prescription Diet c/d Gato',
                'description' => 'Alimento terapéutico para gatos con problemas urinarios',
                'especie' => 'gato',
                'marca_id' => $hills->id,
                'edad' => 'adulto',
                'categoria' => 'Alimentos',
                'precio' => 95000.00,
                'destacado' => false,
                'presentaciones' => [
                    [
                        'nombre' => '4kg',
                        'precio_unitario' => 95000.00,
                        'stock_poblado' => 12,
                        'stock_plaza' => 8,
                    ],
                    [
                        'nombre' => '1.5kg',
                        'precio_unitario' => 38000.00,
                        'stock_poblado' => 20,
                        'stock_plaza' => 25,
                    ]
                ]
            ],
            [
                'nombre' => 'Purina Pro Plan Puppy',
                'description' => 'Alimento para cachorros de todas las razas',
                'especie' => 'perro',
                'marca_id' => $purina->id,
                'edad' => 'cachorro',
                'categoria' => 'Alimentos',
                'precio' => 125000.00,
                'destacado' => true,
                'presentaciones' => [
                    [
                        'nombre' => '15kg',
                        'precio_unitario' => 125000.00,
                        'stock_poblado' => 18,
                        'stock_plaza' => 22,
                    ],
                    [
                        'nombre' => '3kg',
                        'precio_unitario' => 28000.00,
                        'stock_poblado' => 35,
                        'stock_plaza' => 28,
                    ]
                ]
            ],
            [
                'nombre' => 'Bayer Advantix Pipeta',
                'description' => 'Antiparasitario externo para perros',
                'especie' => 'perro',
                'marca_id' => $bayer->id,
                'edad' => 'adulto',
                'categoria' => 'Medicamentos',
                'precio' => 45000.00,
                'destacado' => false,
                'presentaciones' => [
                    [
                        'nombre' => 'Grande (25-40kg)',
                        'precio_unitario' => 45000.00,
                        'stock_poblado' => 15,
                        'stock_plaza' => 10,
                    ],
                    [
                        'nombre' => 'Mediano (10-25kg)',
                        'precio_unitario' => 38000.00,
                        'stock_poblado' => 20,
                        'stock_plaza' => 18,
                    ],
                    [
                        'nombre' => 'Pequeño (4-10kg)',
                        'precio_unitario' => 32000.00,
                        'stock_poblado' => 25,
                        'stock_plaza' => 22,
                    ]
                ]
            ],
            [
                'nombre' => 'Collar Antipulgas',
                'description' => 'Collar antiparasitario, duración 8 meses',
                'especie' => 'gato',
                'marca_id' => $bayer->id,
                'edad' => 'adulto',
                'categoria' => 'Accesorios',
                'precio' => 28000.00,
                'destacado' => false,
                'presentaciones' => [
                    [
                        'nombre' => 'Gato',
                        'precio_unitario' => 28000.00,
                        'stock_poblado' => 30,
                        'stock_plaza' => 25,
                    ],
                    [
                        'nombre' => 'Perro Pequeño',
                        'precio_unitario' => 32000.00,
                        'stock_poblado' => 20,
                        'stock_plaza' => 15,
                    ]
                ]
            ],
            [
                'nombre' => 'Juguete Kong Classic',
                'description' => 'Juguete resistente para perros, ideal para masticar',
                'especie' => 'perro',
                'marca_id' => $purina->id,
                'edad' => 'adulto',
                'categoria' => 'Juguetes',
                'precio' => 35000.00,
                'destacado' => true,
                'presentaciones' => [
                    [
                        'nombre' => 'Grande',
                        'precio_unitario' => 35000.00,
                        'stock_poblado' => 12,
                        'stock_plaza' => 8,
                    ],
                    [
                        'nombre' => 'Mediano',
                        'precio_unitario' => 28000.00,
                        'stock_poblado' => 18,
                        'stock_plaza' => 15,
                    ],
                    [
                        'nombre' => 'Pequeño',
                        'precio_unitario' => 22000.00,
                        'stock_poblado' => 25,
                        'stock_plaza' => 20,
                    ]
                ]
            ],
        ];

        $barcode = 1000000;

        foreach ($productos as $productoData) {
            // Extraer presentaciones del array
            $presentaciones = $productoData['presentaciones'];
            unset($productoData['presentaciones']);

            // Crear el producto
            $producto = Producto::create($productoData);

            // Crear presentaciones y asignar stock por sucursal
            foreach ($presentaciones as $presentacionData) {
                $stockPoblado = $presentacionData['stock_poblado'];
                $stockPlaza = $presentacionData['stock_plaza'];
                
                unset($presentacionData['stock_poblado']);
                unset($presentacionData['stock_plaza']);

                // Crear la presentación
                $presentacion = $producto->presentaciones()->create(array_merge($presentacionData, [
                    'cantidad_inventario' => 0, // Este campo se mantiene en 0, el stock se maneja por sucursal
                    'codigo_barras' => (string) $barcode++,
                ]));

                // Asignar stock por sucursal en la tabla pivot
                if ($sucursalPoblado) {
                    $presentacion->sucursales()->attach($sucursalPoblado->id, [
                        'stock' => $stockPoblado,
                        'stock_minimo' => 5,
                        'stock_maximo' => $stockPoblado * 2,
                        'precio_venta' => $presentacionData['precio_unitario'],
                        'precio_compra' => $presentacionData['precio_unitario'] * 0.75,
                        'activo' => true,
                    ]);
                }

                if ($sucursalPlaza) {
                    $presentacion->sucursales()->attach($sucursalPlaza->id, [
                        'stock' => $stockPlaza,
                        'stock_minimo' => 5,
                        'stock_maximo' => $stockPlaza * 2,
                        'precio_venta' => $presentacionData['precio_unitario'] * 1.05, // Precio ligeramente diferente por sucursal
                        'precio_compra' => $presentacionData['precio_unitario'] * 0.75,
                        'activo' => true,
                    ]);
                }
            }
        }
    }
}
