<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Cliente;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'estado',
        'sucursal_id',
        'password_changed_at',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCliente()
    {
        return $this->role === 'cliente';
    }

    public function isVeterinario()
    {
        return $this->role === 'veterinario';
    }

    public function isPeluquero()
    {
        return $this->role === 'peluquero';
    }

    public function isFacturador()
    {
        return $this->role === 'facturador';
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function citasAsignadas()
    {
        return $this->hasMany(Citas::class, 'veterinario_id');
    }

    public function consultasMedicas()
    {
        return $this->hasMany(ConsultaMedica::class, 'veterinario_id');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'veterinario_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function isAdminSucursal()
    {
        return $this->role === 'admin_sucursal';
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
        ];
    }
}
