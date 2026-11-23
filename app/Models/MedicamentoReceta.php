<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentoReceta extends Model
{
    use HasFactory;

    protected $table = 'medicamentos_receta';

    protected $fillable = [
        'receta_medica_id',
        'nombre_medicamento',
        'principio_activo',
        'concentracion',
        'forma_farmaceutica',
        'dosis',
        'frecuencia',
        'duracion_tratamiento',
        'cantidad_total',
        'instrucciones_uso',
        'contraindicaciones',
    ];

    public function recetaMedica()
    {
        return $this->belongsTo(RecetaMedica::class);
    }
}