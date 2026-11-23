<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Presentacion extends Model
{
    protected $table = 'presentaciones';
    
    protected $fillable = [
        'producto_id',
        'nombre',
        'precio_unitario',
        'cantidad_inventario',
        'imagen',
        'codigo_barras'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'presentacion_sucursal')
                    ->withPivot('stock', 'stock_minimo', 'stock_maximo', 'precio_venta', 'precio_compra', 'activo')
                    ->withTimestamps();
    }

    // Método para obtener el stock total de todas las sucursales
    public function getStockTotalAttribute()
    {
        return $this->sucursales()->sum('presentacion_sucursal.stock');
    }
}