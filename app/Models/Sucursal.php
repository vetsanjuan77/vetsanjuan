<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'telefono',
        'email',
        'ciudad',
        'departamento',
        'nit',
        'regimen_tributario',
        'activa',
        'configuracion',
        'latitud',
        'longitud',
        'user_id',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'configuracion' => 'array',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    // Relaciones
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(ProductoSucursal::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function movimientosCaja(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function presentaciones(): BelongsToMany
    {
        return $this->belongsToMany(Presentacion::class, 'presentacion_sucursal')
                    ->withPivot('stock', 'stock_minimo', 'stock_maximo', 'precio_venta', 'precio_compra', 'activo')
                    ->withTimestamps();
    }

    // Métodos de utilidad
    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function getConfiguracion(string $key, $default = null)
    {
        return data_get($this->configuracion, $key, $default);
    }

    public function setConfiguracion(string $key, $value): void
    {
        $configuracion = $this->configuracion ?? [];
        data_set($configuracion, $key, $value);
        $this->configuracion = $configuracion;
        $this->save();
    }
}
