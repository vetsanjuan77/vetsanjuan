<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaMedica extends Model
{
    use HasFactory;

    protected $table = 'recetas_medicas';

    protected $fillable = [
        'consulta_medica_id',
        'veterinario_id',
        'mascota_id',
        'cliente_id',
        'numero_receta',
        'fecha_emision',
        'fecha_vencimiento',
        'medicamentos',
        'instrucciones',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'medicamentos' => 'array',
    ];

    public function consultaMedica()
    {
        return $this->belongsTo(ConsultaMedica::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascotas::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function medicamentosReceta()
    {
        return $this->hasMany(MedicamentoReceta::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receta) {
            $receta->numero_receta = 'REC-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}