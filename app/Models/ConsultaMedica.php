<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaMedica extends Model
{
    use HasFactory;

    protected $table = 'consultas_medicas';

    protected $fillable = [
        'cita_id',
        'veterinario_id',
        'mascota_id',
        'cliente_id',
        'motivo_consulta',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'peso_actual',
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'estado_general',
        'fecha_consulta',
        'proxima_cita',
    ];

    protected $casts = [
        'fecha_consulta' => 'datetime',
        'proxima_cita' => 'datetime',
        'peso_actual' => 'decimal:2',
        'temperatura' => 'decimal:1',
    ];

    public function cita()
    {
        return $this->belongsTo(Citas::class);
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

    public function recetas()
    {
        return $this->hasMany(RecetaMedica::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}