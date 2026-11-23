<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->constrained('users'); // Usuario responsable de la caja
            $table->foreignId('user_apertura_id')->nullable()->constrained('users'); // Usuario que abre la caja
            $table->foreignId('user_cierre_id')->nullable()->constrained('users'); // Usuario que cierra la caja
            $table->datetime('fecha_apertura');
            $table->datetime('fecha_cierre')->nullable();
            $table->decimal('saldo_inicial', 10, 2);
            $table->decimal('ingresos_efectivo', 10, 2)->default(0);
            $table->decimal('ingresos_tarjeta', 10, 2)->default(0);
            $table->decimal('ingresos_transferencia', 10, 2)->default(0);
            $table->decimal('egresos_efectivo', 10, 2)->default(0);
            $table->decimal('saldo_final', 10, 2)->nullable();
            $table->decimal('saldo_fisico', 10, 2)->nullable(); // Conteo físico al cierre
            $table->decimal('diferencia', 10, 2)->nullable(); // Diferencia entre saldo final y físico
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('caja');
    }
};