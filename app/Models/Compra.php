<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_compra',
        'proveedor_id',
        'sucursal_id',
        'user_id',
        'fecha_compra',
        'fecha_recepcion',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'estado',
        'numero_factura_proveedor',
        'observaciones',
        'documentos_soporte'
    ];

    protected $casts = [
        'fecha_compra' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'documentos_soporte' => 'array',
    ];

    // Relaciones
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
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
        return $this->hasMany(CompraDetalle::class);
    }

    // Métodos de utilidad
    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isRecibida(): bool
    {
        return $this->estado === 'recibida';
    }
}
