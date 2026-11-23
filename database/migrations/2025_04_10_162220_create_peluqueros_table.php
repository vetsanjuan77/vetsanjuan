<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Los peluqueros ahora se manejan en la tabla users con role='peluquero'
        // Esta migración se mantiene para compatibilidad pero no crea tabla
    }

    public function down()
    {
        // No hay tabla que eliminar
    }
};