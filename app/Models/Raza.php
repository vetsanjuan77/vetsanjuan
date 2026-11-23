<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raza extends Model
{
    use HasFactory;

    protected $table = 'razas';

    protected $fillable = [
        'nombre_raza',
        'descripcion',
        'especie_id'
    ];

    public function mascotas()
    {
        return $this->hasMany(Mascotas::class, 'raza_id');
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class);
    }
}
