<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cirugia extends Model
{
    use HasFactory;

    protected $table = 'cirugias';

    protected $fillable = [
        'mascota_id',
        'veterinario_id',
        'cliente_id',
        'tipo_procedimiento',
        'nombre_cirugia',
        'descripcion',
        'fecha_programada',
        'fecha_realizada',
        'duracion_estimada',
        'duracion_real',
        'estado',
        'anestesia_utilizada',
        'complicaciones',
        'observaciones_preoperatorias',
        'observaciones_postoperatorias',
        'cuidados_postoperatorios',
        'costo_estimado',
        'costo_final',
        'material_utilizado',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_realizada' => 'datetime',
        'costo_estimado' => 'decimal:2',
        'costo_final' => 'decimal:2',
        'material_utilizado' => 'array',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascotas::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoCirugia::class);
    }
}