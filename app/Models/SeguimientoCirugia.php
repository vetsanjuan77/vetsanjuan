<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoCirugia extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_cirugia';

    protected $fillable = [
        'cirugia_id',
        'veterinario_id',
        'fecha_seguimiento',
        'estado_paciente',
        'signos_vitales',
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'nivel_dolor',
        'apetito',
        'actividad',
        'herida_quirurgica',
        'complicaciones',
        'medicamentos_administrados',
        'observaciones',
        'proxima_revision',
        'recomendaciones',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
        'proxima_revision' => 'datetime',
        'temperatura' => 'decimal:1',
        'medicamentos_administrados' => 'array',
    ];

    public function cirugia()
    {
        return $this->belongsTo(Cirugia::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    public function isRecuperacionNormal(): bool
    {
        return $this->estado_paciente === 'estable' && 
               $this->nivel_dolor <= 3 && 
               $this->herida_quirurgica === 'cicatrizando_bien';
    }

    public function requiereAtencionUrgente(): bool
    {
        return $this->estado_paciente === 'critico' || 
               $this->nivel_dolor >= 8 || 
               $this->herida_quirurgica === 'infectada';
    }
}