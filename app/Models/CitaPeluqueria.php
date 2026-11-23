<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitaPeluqueria extends Model
{
    use HasFactory;

    protected $table = 'citas_peluqueria';

    protected $fillable = [
        'fecha',
        'hora',
        'tipo_servicio',
        'estado',
        'motivo',
        'precio',
        'observaciones',
        'cliente_id',
        'mascota_id',
        'peluquero_id',
        'servicio_id',
        // Nuevos campos resumen cliente
        'nombre_cliente',
        'correo_cliente',
        'telefono_cliente',
        'tiene_nudos',
        // Tarifa de agendamiento
        'reserva_consumibles',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio' => 'decimal:2',
        'reserva_consumibles' => 'decimal:2',
        'tiene_nudos' => 'boolean',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mascota(): BelongsTo
    {
        return $this->belongsTo(Mascotas::class);
    }

    public function peluquero(): BelongsTo
    {
        return $this->belongsTo(Peluquero::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicios::class, 'servicio_id');
    }

    // Métodos de utilidad para el estado
    public function isProgramada(): bool
    {
        return $this->estado === 'programada';
    }

    public function isConfirmada(): bool
    {
        return $this->estado === 'confirmada';
    }

    public function isEnProceso(): bool
    {
        return $this->estado === 'en_proceso';
    }

    public function isCompletada(): bool
    {
        return $this->estado === 'completada';
    }

    public function isCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }

    // Scopes
    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programada');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeDelPeluquero($query, $peluqueroId)
    {
        return $query->where('peluquero_id', $peluqueroId);
    }

    public function scopeDelDia($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function getFechaHoraAttribute()
    {
        if (!$this->fecha || !$this->hora) {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($this->fecha . ' ' . $this->hora);
        } catch (\Exception $e) {
            return null;
        }
    }
}