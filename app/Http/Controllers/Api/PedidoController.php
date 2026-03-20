<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    // 1. Ver historial de pedidos de un cliente (GET)
    public function index()
    {
        $cliente = Auth::guard('api-clientes')->user();
        
        // Trae los pedidos del cliente, incluyendo los detalles y los datos de cada producto
        $pedidos = Pedido::with('detalles.producto')
                         ->where('id_cliente', $cliente->id)
                         ->orderBy('created_at', 'desc')
                         ->get();

        return response()->json([
            "status" => true,
            "data" => $pedidos
        ]);
    }

    // 2. Crear pedido por cliente (POST)
    public function store(Request $request)
    {
        $cliente = Auth::guard('api-clientes')->user();

        // Validamos que nos envíen un arreglo de productos con cantidad
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);

        DB::beginTransaction(); // Protegemos la transacción
        try {
            $total = 0;

            // Creamos el pedido inicial en $0
            $pedido = Pedido::create([
                'id_cliente' => $cliente->id,
                'total' => 0,
                'estado' => 'creado'
            ]);

            // Guardamos cada producto en detalles_pedidos y calculamos el total
            foreach ($request->productos as $item) {
                $producto = Producto::where('id_producto', $item['id_producto'])->first();
                $subtotal = $producto->precio_original * $item['cantidad'];
                $total += $subtotal;

                DetallePedido::create([
                    'id_pedido' => $pedido->id,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_original
                ]);
            }

            // Actualizamos el total final del pedido
            $pedido->total = $total;
            $pedido->save();

            DB::commit();

            return response()->json([
                "status" => true,
                "mensaje" => "Pedido creado exitosamente",
                "data" => $pedido->load('detalles') // Devuelve el pedido con sus productos
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => false,
                "mensaje" => "Error al crear el pedido",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // 3. Ver detalle de pedido de un cliente (GET)
    public function show($id)
    {
        $cliente = Auth::guard('api-clientes')->user();
        
        // Busca el pedido, pero SOLO si pertenece al cliente autenticado
        $pedido = Pedido::with('detalles.producto')
                        ->where('id_cliente', $cliente->id)
                        ->find($id);

        if (!$pedido) {
            return response()->json(["status" => false, "mensaje" => "Pedido no encontrado o acceso denegado"], 404);
        }

        return response()->json(["status" => true, "data" => $pedido]);
    }

    // 4. Cancelar pedido por cliente (DELETE)
    public function destroy($id)
    {
        $cliente = Auth::guard('api-clientes')->user();
        $pedido = Pedido::where('id_cliente', $cliente->id)->find($id);

        if (!$pedido) {
            return response()->json(["status" => false, "mensaje" => "Pedido no encontrado"], 404);
        }

        // Para mantener historial, en lugar de borrarlo físico, actualizamos su estado:
        $pedido->estado = 'cancelado';
        $pedido->save();

        return response()->json([
            "status" => true,
            "mensaje" => "El pedido ha sido cancelado exitosamente"
        ]);
    }
}