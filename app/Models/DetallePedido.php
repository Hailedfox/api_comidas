<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalles_pedidos';
    protected $fillable = ['id_pedido', 'id_producto', 'cantidad', 'precio_unitario'];

    // Relación: Este detalle pertenece a un producto específico
    public function producto() {
        // Asumiendo que tu PK en productos es id_producto según el error anterior
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto'); 
    }
}