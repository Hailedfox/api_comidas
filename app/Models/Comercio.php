<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    use HasFactory;

    protected $table = 'comercios';
    protected $primaryKey = 'id_comercio';
    
    public $timestamps = false; // Tu tabla comercios no tiene created_at

    protected $fillable = [
        'id_usuario',
        'nombre',
        'descripcion',
        'direccion',
        'ciudad',
        'horario_apertura',
        'horario_cierre',
        'activo',
        'foto',
    ];

    // Convierte 'activo' a true/false en lugar de 1/0
    protected $casts = [
        'activo' => 'boolean',
    ];

    // --- RELACIONES ---
    // Un comercio pertenece a un usuario (Dueño)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    // Un comercio tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_comercio', 'id_comercio');
    }
}