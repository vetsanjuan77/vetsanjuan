<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mascotas extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nombre_mascota',
        'fecha_nacimiento',
        'cliente_id',
        'especie_id',
        'sexo_id',
        'edad',
        'peso',
        'alergias',
        'comentarios',
        'foto',
        'tamano_id',
        'tipo_pelo_id',
        'raza_id'
    ];

    /**
     * Obtener la URL completa de la foto de la mascota
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }
        return asset('images/default-pet.jpg'); // Imagen por defecto
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class);
    }


    public function sexo()
    {
        return $this->belongsTo(Sexo::class);
    }


 
    public function citas()
    {
        return $this->hasMany(Citas::class);
    }

    public function vacunas()
    {
        return $this->belongsToMany(Vacuna::class, 'mascota_vacuna', 'mascota_id', 'vacuna_id');
    }

    public function tamano()
    {
        return $this->belongsTo(Tamano::class);
    }

    public function tipoPelo()
    {
        return $this->belongsTo(TipoPelo::class, 'tipo_pelo_id');
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class);
    }
}
