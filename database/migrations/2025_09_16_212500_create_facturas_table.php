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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('consulta_medica_id')->nullable()->constrained('consultas_medicas');
            $table->foreignId('cirugia_id')->nullable()->constrained('cirugias');
            $table->datetime('fecha_emision');
            $table->datetime('fecha_vencimiento')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'pagada', 'cancelada', 'vencida'])->default('pendiente');
            $table->string('metodo_pago')->nullable();
            $table->datetime('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            // Campos DIAN añadidos directamente en la creación de la tabla
            $table->string('cufe')->nullable();
            $table->string('zip_key')->nullable();
            $table->string('qr')->nullable();
            $table->string('estado_dian')->default('sin_enviar');
            $table->string('tipo_documento')->nullable();
            $table->string('numero_dian')->nullable();
            $table->json('respuesta_dian')->nullable();

            // Separación de flujos
            $table->enum('tipo_venta', ['virtual', 'presencial'])->default('presencial')->index();

            // Datos de envío para ventas virtuales
            $table->string('envio_contacto_nombre')->nullable();
            $table->string('envio_contacto_telefono')->nullable();
            $table->string('envio_departamento')->nullable();
            $table->string('envio_ciudad')->nullable();
            $table->string('envio_direccion')->nullable();
            $table->string('envio_codigo_postal')->nullable();

            $table->timestamps();
        });

        Schema::create('detalle_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('presentacion_id')->nullable()->constrained('presentaciones');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_facturas');
        Schema::dropIfExists('facturas');
    }
};
