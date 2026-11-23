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
        Schema::create('razas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_raza');
            $table->string('descripcion')->nullable();
            $table->foreignId('especie_id')->references('id')->on('especie');
            $table->timestamps();
            
            // Índice único compuesto para evitar razas duplicadas por especie
            $table->unique(['nombre_raza', 'especie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razas');
    }
};
