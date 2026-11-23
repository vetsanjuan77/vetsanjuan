<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioTrabajo extends Model
{
    protected $fillable = [
        'user_id',
        'rol',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'intervalo_minutos',
        'activo',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fin' => 'datetime:H:i:s',
        'intervalo_minutos' => 'integer',
        'activo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
