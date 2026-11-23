<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'cliente_id',
        'sucursal_id',
        'consulta_medica_id',
        'cirugia_id',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'estado',
        'metodo_pago',
        'fecha_pago',
        'observaciones',
        // DIAN
        'cufe',
        'zip_key',
        'qr',
        'estado_dian',
        'respuesta_dian',
        'tipo_documento',
        'numero_dian',
        // Nuevos campos
        'tipo_venta',
        'envio_contacto_nombre',
        'envio_contacto_telefono',
        'envio_departamento',
        'envio_ciudad',
        'envio_direccion',
        'envio_codigo_postal',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_pago' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'respuesta_dian' => 'array',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consultaMedica()
    {
        return $this->belongsTo(ConsultaMedica::class);
    }

    public function cirugia()
    {
        return $this->belongsTo(Cirugia::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleFactura::class);
    }

    public function isPagada(): bool
    {
        return $this->estado === 'pagada';
    }

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isVencida(): bool
    {
        return $this->fecha_vencimiento < now() && !$this->isPagada();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($factura) {
            $factura->numero_factura = 'FAC-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}