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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->foreignId('cliente_id')->references('id')->on('clientes');
            $table->foreignId('mascota_id')->references('id')->on('mascotas');
            $table->foreignId('veterinario_id')->references('id')->on('users');
            $table->foreignId('tipo_servicio_id')->references('id')->on('servicios');
            $table->string('tipo_servicio')->default('consulta');
            $table->enum('estado', ['programada', 'confirmada', 'en_proceso', 'completada', 'cancelada', 'no_asistio'])->default('programada');
            $table->text('motivo')->nullable();
            $table->decimal('precio', 10, 2)->nullable();
            // Nuevo: tarifa de agendamiento (consumibles, no reembolsable)
            $table->decimal('reserva_consumibles', 10, 2)->default(10000);
            $table->timestamps();

            // Índices y restricción única para evitar doble reserva por veterinario y horario
            $table->index(['fecha']);
            $table->index(['veterinario_id']);
            $table->unique(['veterinario_id', 'fecha', 'hora'], 'citas_unique_vet_fecha_hora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
