<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraDetalle extends Model
{
    use HasFactory;

    protected $table = 'compra_detalle';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad_pedida',
        'cantidad_recibida',
        'precio_unitario',
        'subtotal',
        'fecha_vencimiento',
        'lote'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fecha_vencimiento' => 'datetime',
    ];

    // Relaciones
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Métodos de utilidad
    public function isCompleto(): bool
    {
        return $this->cantidad_recibida >= $this->cantidad_pedida;
    }

    public function isParcial(): bool
    {
        return $this->cantidad_recibida > 0 && $this->cantidad_recibida < $this->cantidad_pedida;
    }

    public function isPendiente(): bool
    {
        return $this->cantidad_recibida == 0;
    }

    public function getCantidadPendiente(): int
    {
        return $this->cantidad_pedida - $this->cantidad_recibida;
    }
}
