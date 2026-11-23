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
        Schema::create('seguimientos_cirugia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cirugia_id')->constrained('cirugias');
            $table->foreignId('veterinario_id')->constrained('users');
            $table->datetime('fecha_seguimiento');
            $table->enum('estado_paciente', ['estable', 'mejorando', 'preocupante', 'critico'])->default('estable');
            $table->text('signos_vitales')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->integer('nivel_dolor')->default(0); // escala 0-10
            $table->enum('apetito', ['normal', 'reducido', 'ausente'])->default('normal');
            $table->enum('actividad', ['normal', 'reducida', 'letargico', 'muy_activo'])->default('normal');
            $table->enum('herida_quirurgica', ['cicatrizando_bien', 'inflamada', 'infectada', 'dehiscencia'])->default('cicatrizando_bien');
            $table->text('complicaciones')->nullable();
            $table->json('medicamentos_administrados')->nullable();
            $table->text('observaciones')->nullable();
            $table->datetime('proxima_revision')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos_cirugia');
    }
};
