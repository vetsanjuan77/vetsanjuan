<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_gasto')->unique();
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->constrained('users'); // Usuario que registra el gasto
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');
            $table->string('categoria'); // servicios, suministros, mantenimiento, etc.
            $table->string('concepto');
            $table->text('descripcion')->nullable();
            $table->datetime('fecha_gasto');
            $table->decimal('valor', 10, 2);
            $table->decimal('monto', 10, 2); // Alias para valor para compatibilidad
            $table->string('metodo_pago'); // efectivo, transferencia, cheque, etc.
            $table->string('numero_documento')->nullable(); // número de factura, recibo, etc.
            $table->json('documentos_soporte')->nullable(); // Para almacenar rutas de documentos
            $table->enum('estado', ['pendiente', 'aprobado', 'pagado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos');
    }
};