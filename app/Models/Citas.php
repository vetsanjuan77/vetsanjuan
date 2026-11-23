<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Mascotas;
use App\Models\User;
use App\Models\Servicios;

class Citas extends Model
{
    protected $fillable = [
        'fecha',
        'hora',
        'cliente_id',
        'mascota_id',
        'tipo_servicio_id',
        'tipo_servicio',
        'veterinario_id',
        'estado',
        'motivo',
        'precio',
        // Nuevo: tarifa de agendamiento (consumibles)
        'reserva_consumibles',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio' => 'decimal:2',
        'reserva_consumibles' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mascota()
    {
        return $this->belongsTo(Mascotas::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicios::class, 'tipo_servicio_id');
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    // Métodos de utilidad para el estado
    public function isProgramada()
    {
        return $this->estado === 'programada';
    }

    public function isConfirmada()
    {
        return $this->estado === 'confirmada';
    }

    public function isEnProceso()
    {
        return $this->estado === 'en_proceso';
    }

    public function isCompletada()
    {
        return $this->estado === 'completada';
    }

    public function isCancelada()
    {
        return $this->estado === 'cancelada';
    }

    public function isNoAsistio()
    {
        return $this->estado === 'no_asistio';
    }
}
