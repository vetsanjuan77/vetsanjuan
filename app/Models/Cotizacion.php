<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'numero_cotizacion',
        'cliente_id',
        'sucursal_id',
        'user_id',
        'fecha_cotizacion',
        'fecha_vencimiento',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'estado',
        'observaciones',
        'terminos_condiciones'
    ];

    protected $casts = [
        'fecha_cotizacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CotizacionDetalle::class);
    }

    // Métodos de utilidad
    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isAprobada(): bool
    {
        return $this->estado === 'aprobada';
    }

    public function isVencida(): bool
    {
        return $this->estado === 'vencida' || $this->fecha_vencimiento < now();
    }
}
