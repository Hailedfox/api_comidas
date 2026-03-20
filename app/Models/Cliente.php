<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens; 

class Cliente extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'clientes'; 

    // ¡ESTA ES LA PARTE IMPORTANTE!
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'imagen_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}