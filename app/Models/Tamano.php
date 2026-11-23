<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tamano extends Model
{
    use HasFactory;

    protected $table = 'tamanos';

    protected $fillable = [
        'nombre_tamano',
        'descripcion'
    ];

    public function mascotas()
    {
        return $this->hasMany(Mascotas::class, 'tamano_id');
    }
}
