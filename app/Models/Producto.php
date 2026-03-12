<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'id_comercio',
        'id_categoria',
        'nombre',
        'descripcion',
        'precio_original',
        'precio_descuento',
        'cantidad_disponible',
        'fecha_caducidad',
        'hora_recogida_inicio',
        'hora_recogida_fin',
        'activo'
    ];

    protected $casts = [
        'precio_original' => 'decimal:2',
        'precio_descuento' => 'decimal:2',
        'fecha_caducidad' => 'date',
        'activo' => 'boolean',
    ];

    public function comercio()
    {
        return $this->belongsTo(Comercio::class, 'id_comercio', 'id_comercio');
    }
}