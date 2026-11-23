<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'nit',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'contacto_nombre',
        'contacto_telefono',
        'contacto_email',
        'tipo',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    // Métodos de utilidad
    public function isActivo(): bool
    {
        return $this->activo;
    }
}
