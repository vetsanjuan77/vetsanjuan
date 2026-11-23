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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('direccion');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('ciudad');
            $table->string('departamento');
            $table->string('nit')->nullable();
            $table->string('regimen_tributario')->default('simplificado');
            $table->boolean('activa')->default(true);
            $table->json('configuracion')->nullable(); // Para configuraciones específicas
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
