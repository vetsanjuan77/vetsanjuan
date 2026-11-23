<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Peluquero extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'estado',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('peluquero', function ($builder) {
            $builder->where('role', 'peluquero');
        });
    }

    public function citasPeluqueria()
    {
        return $this->hasMany(CitaPeluqueria::class, 'peluquero_id');
    }

    public function hasChangedPassword()
    {
        return !is_null($this->password_changed_at);
    }

    public function markPasswordAsChanged()
    {
        $this->password_changed_at = now();
        $this->save();
    }

    public function isActivo()
    {
        return $this->estado === 'activo';
    }

    public function isInactivo()
    {
        return $this->estado === 'inactivo';
    }

    public function isSuspendido()
    {
        return $this->estado === 'suspendido';
    }
}