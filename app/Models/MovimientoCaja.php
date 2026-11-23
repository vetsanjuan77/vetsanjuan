<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id',
        'factura_id',
        'gasto_id',
        'tipo',
        'concepto',
        'descripcion',
        'valor',
        'metodo_pago',
        'fecha_movimiento',
        'numero_documento'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    // Relaciones
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class);
    }

    // Métodos de utilidad
    public function isIngreso(): bool
    {
        return $this->tipo === 'ingreso';
    }

    public function isEgreso(): bool
    {
        return $this->tipo === 'egreso';
    }

    public function getValorFormateado(): string
    {
        return '$' . number_format($this->valor, 2, ',', '.');
    }
}
