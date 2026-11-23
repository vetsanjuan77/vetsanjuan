<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("cliente_id");
            $table->foreign("cliente_id") -> references("id") -> on("clientes");
            
            $table->unsignedBigInteger("sucursal_id");
            
            $table->date("fecha");

            // Campos de dirección de envío
            $table->string("departamento")->nullable();
            $table->string("ciudad")->nullable();
            $table->string("codigo_postal")->nullable();
            $table->string("tipo_calle")->nullable();
            $table->string("direccion")->nullable();
            $table->text("referencias")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venta');
    }
};