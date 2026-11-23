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
        Schema::create('horario_trabajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('rol', ['veterinario', 'peluquero']);
            $table->unsignedTinyInteger('dia_semana'); // 0 (domingo) - 6 (sábado)
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedSmallInteger('intervalo_minutos')->default(60);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'rol', 'dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_trabajos');
    }
};
