<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Producto;
use Carbon\Carbon;

class CompraSeeder extends Seeder
{
    public function run()
    {
        // Obtener datos necesarios
        $proveedor1 = Proveedor::where('nombre', 'Distribuidora Pet Food S.A.S')->first();
        $proveedor2 = Proveedor::where('nombre', 'Laboratorios Veterinarios Ltda')->first();
        $sucursal = Sucursal::first();
        $usuario = User::where('role', 'admin')->first();
        $productos = Producto::limit(5)->get();

        if (!$proveedor1 || !$proveedor2 || !$sucursal || !$usuario || $productos->count() < 3) {
            $this->command->warn('No se pueden crear compras: faltan datos base (proveedores, sucursales, usuarios o productos)');
            return;
        }

        // Compra 1 - Recibida
        $compra1 = Compra::create([
            'numero_compra' => 'C-2024-001',
            'proveedor_id' => $proveedor1->id,
            'sucursal_id' => $sucursal->id,
            'user_id' => $usuario->id,
            'fecha_compra' => Carbon::now()->subDays(15),
            'fecha_recepcion' => Carbon::now()->subDays(10),
            'subtotal' => 450000.00,
            'impuestos' => 85500.00,
            'descuento' => 0.00,
            'total' => 535500.00,
            'estado' => 'recibida',
            'numero_factura_proveedor' => 'FV-2024-0156',
            'observaciones' => 'Compra de alimentos premium - entrega completa',
            'documentos_soporte' => ['factura_proveedor.pdf', 'remision.pdf']
        ]);

        // Detalles de compra 1
        if ($productos->count() > 0) {
            CompraDetalle::create([
                'compra_id' => $compra1->id,
                'producto_id' => $productos[0]->id,
                'cantidad_pedida' => 20,
                'cantidad_recibida' => 20,
                'precio_unitario' => 15000.00,
                'subtotal' => 300000.00,
                'fecha_vencimiento' => Carbon::now()->addMonths(18),
                'lote' => 'LOT-2024-A001'
            ]);

            if ($productos->count() > 1) {
                CompraDetalle::create([
                    'compra_id' => $compra1->id,
                    'producto_id' => $productos[1]->id,
                    'cantidad_pedida' => 10,
                    'cantidad_recibida' => 10,
                    'precio_unitario' => 15000.00,
                    'subtotal' => 150000.00,
                    'fecha_vencimiento' => Carbon::now()->addMonths(12),
                    'lote' => 'LOT-2024-B002'
                ]);
            }
        }

        // Compra 2 - Pendiente
        $compra2 = Compra::create([
            'numero_compra' => 'C-2024-002',
            'proveedor_id' => $proveedor2->id,
            'sucursal_id' => $sucursal->id,
            'user_id' => $usuario->id,
            'fecha_compra' => Carbon::now()->subDays(5),
            'fecha_recepcion' => null,
            'subtotal' => 280000.00,
            'impuestos' => 53200.00,
            'descuento' => 15000.00,
            'total' => 318200.00,
            'estado' => 'pendiente',
            'numero_factura_proveedor' => null,
            'observaciones' => 'Pedido de medicamentos - pendiente de entrega',
            'documentos_soporte' => []
        ]);

        // Detalles de compra 2
        if ($productos->count() > 2) {
            CompraDetalle::create([
                'compra_id' => $compra2->id,
                'producto_id' => $productos[2]->id,
                'cantidad_pedida' => 15,
                'cantidad_recibida' => 0,
                'precio_unitario' => 12000.00,
                'subtotal' => 180000.00,
                'fecha_vencimiento' => Carbon::now()->addMonths(24),
                'lote' => null
            ]);

            if ($productos->count() > 3) {
                CompraDetalle::create([
                    'compra_id' => $compra2->id,
                    'producto_id' => $productos[3]->id,
                    'cantidad_pedida' => 8,
                    'cantidad_recibida' => 0,
                    'precio_unitario' => 12500.00,
                    'subtotal' => 100000.00,
                    'fecha_vencimiento' => Carbon::now()->addMonths(36),
                    'lote' => null
                ]);
            }
        }

        // Compra 3 - Parcialmente recibida
        $compra3 = Compra::create([
            'numero_compra' => 'C-2024-003',
            'proveedor_id' => $proveedor1->id,
            'sucursal_id' => $sucursal->id,
            'user_id' => $usuario->id,
            'fecha_compra' => Carbon::now()->subDays(8),
            'fecha_recepcion' => Carbon::now()->subDays(3),
            'subtotal' => 320000.00,
            'impuestos' => 60800.00,
            'descuento' => 10000.00,
            'total' => 370800.00,
            'estado' => 'parcial',
            'numero_factura_proveedor' => 'FV-2024-0189',
            'observaciones' => 'Entrega parcial - faltante por confirmar fecha',
            'documentos_soporte' => ['factura_parcial.pdf']
        ]);

        // Detalles de compra 3 (parcial)
        if ($productos->count() > 4) {
            CompraDetalle::create([
                'compra_id' => $compra3->id,
                'producto_id' => $productos[4]->id,
                'cantidad_pedida' => 25,
                'cantidad_recibida' => 15,
                'precio_unitario' => 12800.00,
                'subtotal' => 320000.00,
                'fecha_vencimiento' => Carbon::now()->addMonths(20),
                'lote' => 'LOT-2024-C003'
            ]);
        }
    }
}