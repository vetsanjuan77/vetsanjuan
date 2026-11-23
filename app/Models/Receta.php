<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receta extends Model
{
    use HasFactory;

    protected $fillable = [
        'consulta_id',
        'veterinario_id',
        'numero_receta',
        'fecha_emision',
        'fecha_vencimiento',
        'medicamentos',
        'instrucciones_generales',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'medicamentos' => 'array',
    ];

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(ConsultaMedica::class);
    }

    public function veterinario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    public function isVencida(): bool
    {
        return $this->fecha_vencimiento < now();
    }

    public function isActiva(): bool
    {
        return $this->estado === 'activa' && !$this->isVencida();
    }
}