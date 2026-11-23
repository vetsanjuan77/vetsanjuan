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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_mascota');
            $table->date('fecha_nacimiento');
            $table->foreignId('cliente_id')->references('id')->on('clientes');
            $table->foreignId('especie_id')->references('id')->on('especie');
            $table->foreignId('sexo_id')->references('id')->on('sexo');
            $table->foreignId('tamano_id')->nullable()->references('id')->on('tamanos');
            $table->foreignId('tipo_pelo_id')->nullable()->references('id')->on('tipos_pelo');
            $table->foreignId('raza_id')->nullable()->references('id')->on('razas');
            $table->integer('edad');
            $table->float('peso');
            $table->string('alergias')->nullable();
            $table->string('comentarios')->nullable();
            $table->string('foto')->nullable();
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
        Schema::dropIfExists('mascotas');
    }
};
