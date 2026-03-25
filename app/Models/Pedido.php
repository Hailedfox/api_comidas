<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false; // Tu SQL usa current_timestamp en DB

    protected $fillable = ['id_usuario', 'id_comercio', 'total_pagado', 'estado'];

    // Relación 1 a Muchos: Un pedido tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }
}