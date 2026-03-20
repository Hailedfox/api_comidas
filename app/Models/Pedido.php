<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    // 'estado' puede ser: 'creado', 'cancelado', etc.
    protected $fillable = ['id_cliente', 'total', 'estado']; 

    // Relación: Un pedido pertenece a un cliente
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    // Relación 1 a Muchos: Un pedido tiene muchos detalles (productos)
    public function detalles() {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }
}