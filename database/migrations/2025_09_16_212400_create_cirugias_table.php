<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mascota_id')->constrained('mascotas');
            $table->foreignId('veterinario_id')->constrained('users');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('tipo_procedimiento');
            $table->string('nombre_cirugia');
            $table->text('descripcion')->nullable();
            $table->datetime('fecha_programada');
            $table->datetime('fecha_realizada')->nullable();
            $table->integer('duracion_estimada')->nullable(); // en minutos
            $table->integer('duracion_real')->nullable(); // en minutos
            $table->enum('estado', ['programada', 'en_proceso', 'completada', 'cancelada', 'postergada'])->default('programada');
            $table->string('anestesia_utilizada')->nullable();
            $table->text('complicaciones')->nullable();
            $table->text('observaciones_preoperatorias')->nullable();
            $table->text('observaciones_postoperatorias')->nullable();
            $table->text('cuidados_postoperatorios')->nullable();
            $table->decimal('costo_estimado', 10, 2)->nullable();
            $table->decimal('costo_final', 10, 2)->nullable();
            $table->json('material_utilizado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugias');
    }
};
