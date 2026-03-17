<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; 
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable 
{
     use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users'; 
    protected $primaryKey = 'id'; 

    // En tu tabla 'users' sí tienes created_at y updated_at, así que 
    // Laravel manejará los timestamps automáticamente (no ponemos $timestamps = false)

    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'rol',
        'activo',
        'google_id',
        'foto',
    ];

    // 🔒 MUY IMPORTANTE: Ocultar datos sensibles
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 🔄 Casteos: Convierte los tipos de datos automáticamente
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    // --- MÉTODOS DE ROLES (Actividad 15) ---
    public function esMaster()
    {
        return $this->rol === 'master';
    }

    public function esBase()
    {
        return $this->rol === 'base';
    }

    // --- RELACIONES ---
    // Un usuario puede tener muchos comercios
    public function comercios()
    {
        return $this->hasMany(Comercio::class, 'id_usuario', 'id');
    }
}