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
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas_medicas')->onDelete('cascade');
            $table->foreignId('veterinario_id')->constrained('users')->onDelete('cascade');
            $table->string('numero_receta')->unique();
            $table->datetime('fecha_emision');
            $table->datetime('fecha_vencimiento')->nullable();
            $table->json('medicamentos');
            $table->text('instrucciones_generales')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['activa', 'dispensada', 'vencida', 'cancelada'])->default('activa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
