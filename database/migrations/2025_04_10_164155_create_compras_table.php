<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compra')->unique();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->constrained('users'); // Usuario que registra la compra
            $table->datetime('fecha_compra');
            $table->datetime('fecha_recepcion')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'recibida', 'parcial', 'cancelada'])->default('pendiente');
            $table->string('numero_factura_proveedor')->nullable();
            $table->text('observaciones')->nullable();
            $table->json('documentos_soporte')->nullable(); // Para almacenar rutas de documentos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('compras');
    }
};