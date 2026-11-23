<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPelo extends Model
{
    use HasFactory;

    protected $table = 'tipos_pelo';

    protected $fillable = [
        'nombre_tipo_pelo',
        'descripcion'
    ];

    public function mascotas()
    {
        return $this->hasMany(Mascotas::class, 'tipo_pelo_id');
    }
}
