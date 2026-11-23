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
        Schema::create('citas_peluqueria', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->string('tipo_servicio'); // Baño, corte, baño y corte, etc.
            $table->string('estado')->default('programada'); // programada, confirmada, en_proceso, completada, cancelada
            $table->text('motivo')->nullable();
            $table->decimal('precio', 8, 2)->nullable();
            $table->text('observaciones')->nullable();
            
            // Relaciones
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('mascota_id')->constrained('mascotas')->onDelete('cascade');
            $table->foreignId('peluquero_id')->constrained('users')->onDelete('cascade');
            // Nuevo: servicio_id para relación con servicios
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            
            // Nuevo: resumen del cliente y flags para panel del peluquero
            $table->string('nombre_cliente')->nullable();
            $table->string('correo_cliente')->nullable();
            $table->string('telefono_cliente')->nullable();
            $table->boolean('tiene_nudos')->default(false);
            
            // Nuevo: tarifa de agendamiento (consumibles, no reembolsable)
            $table->decimal('reserva_consumibles', 10, 2)->default(10000);
            
            $table->timestamps();

            // Índices y restricción única para evitar doble reserva por peluquero y horario
            $table->index(['fecha']);
            $table->index(['peluquero_id']);
            $table->unique(['peluquero_id', 'fecha', 'hora'], 'peluqueria_unique_peluquero_fecha_hora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas_peluqueria');
    }
};
