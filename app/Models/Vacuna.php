<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacuna extends Model
{
    protected $table = 'vacunas';
    
    protected $fillable = [
        'nombre',
        'tipo',
        'especie_id'
    ];

    public function especie()
    {
        return $this->belongsTo(Especie::class);
    }

    public function mascotas()
    {
        return $this->belongsToMany(Mascotas::class, 'mascota_vacuna', 'vacuna_id', 'mascota_id');
    }
}
