<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producto_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->default(0);
            $table->decimal('precio_venta', 10, 2)->nullable();
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->unique(['producto_id', 'sucursal_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_sucursal');
    }
};