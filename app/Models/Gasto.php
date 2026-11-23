<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_gasto',
        'sucursal_id',
        'user_id',
        'proveedor_id',
        'categoria',
        'concepto',
        'descripcion',
        'fecha_gasto',
        'valor',
        'metodo_pago',
        'numero_documento',
        'documentos_soporte',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_gasto' => 'datetime',
        'valor' => 'decimal:2',
        'documentos_soporte' => 'array',
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

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Métodos de utilidad
    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isPagado(): bool
    {
        return $this->estado === 'pagado';
    }
}
