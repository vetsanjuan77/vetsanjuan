<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'description',
        'especie',
        'marca_id',
        'categoria',
        'imagen',
        'edad',
        'destacado',
        'precio',
        'cantidad_inventario',
        'codigo_barras',
    ];

    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class);
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class, 'producto_id');
    }
    
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'producto_sucursal')
                    ->withPivot('stock', 'stock_minimo', 'stock_maximo', 'precio_venta', 'precio_compra', 'activo')
                    ->withTimestamps();
    }

    // Método para obtener el stock total de todas las presentaciones en todas las sucursales
    public function getStockTotalAttribute()
    {
        return $this->presentaciones()
                    ->with('sucursales')
                    ->get()
                    ->sum(function($presentacion) {
                        return $presentacion->sucursales->sum('pivot.stock');
                    });
    }

    // Método para obtener el stock mínimo total
    public function getStockMinimoTotalAttribute()
    {
        return $this->presentaciones()
                    ->with('sucursales')
                    ->get()
                    ->sum(function($presentacion) {
                        return $presentacion->sucursales->sum('pivot.stock_minimo');
                    });
    }
}
