<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('caja')->onDelete('cascade');
            $table->foreignId('factura_id')->nullable()->constrained('facturas');
            $table->foreignId('gasto_id')->nullable()->constrained('gastos');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->string('concepto');
            $table->text('descripcion')->nullable();
            $table->decimal('valor', 10, 2);
            $table->string('metodo_pago'); // efectivo, tarjeta, transferencia
            $table->datetime('fecha_movimiento');
            $table->string('numero_documento')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_caja');
    }
};