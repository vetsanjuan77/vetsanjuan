<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'caja';

    protected $fillable = [
        'nombre',
        'sucursal_id',
        'user_id',
        'user_apertura_id',
        'user_cierre_id',
        'fecha_apertura',
        'fecha_cierre',
        'saldo_inicial',
        'ingresos_efectivo',
        'ingresos_tarjeta',
        'ingresos_transferencia',
        'egresos_efectivo',
        'saldo_final',
        'saldo_fisico',
        'diferencia',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'ingresos_efectivo' => 'decimal:2',
        'ingresos_tarjeta' => 'decimal:2',
        'ingresos_transferencia' => 'decimal:2',
        'egresos_efectivo' => 'decimal:2',
        'saldo_final' => 'decimal:2',
        'saldo_fisico' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relaciones
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usuarioApertura(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_apertura_id');
    }

    public function usuarioCierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cierre_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    // Métodos de utilidad
    public function isAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    public function isCerrada(): bool
    {
        return $this->estado === 'cerrada';
    }

    public function calcularSaldoActual(): float
    {
        $ingresos = $this->movimientos()->where('tipo', 'ingreso')->sum('valor');
        $egresos = $this->movimientos()->where('tipo', 'egreso')->sum('valor');
        
        return $this->saldo_inicial + $ingresos - $egresos;
    }
}
