<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    // Ver historial de pedidos
    public function index()
    {
        $pedidos = Pedido::with(['detalles.producto', 'usuario'])->get();
        return response()->json($pedidos, 200);
    }

    // Crear pedido con varios productos (Relación 1 a Muchos)
    public function store(Request $request)
    {
        // Iniciamos una transacción para asegurar que se guarde todo o nada
        return DB::transaction(function () use ($request) {
            // 1. Crear el encabezado del pedido
            $pedido = Pedido::create([
                'id_usuario' => $request->id_usuario,
                'id_comercio' => $request->id_comercio,
                'total_pagado' => $request->total_pagado,
                'estado' => 'pendiente'
            ]);

            // 2. Crear los detalles (Varios productos)
            foreach ($request->productos as $item) {
                DetallePedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);
            }

            return response()->json(['message' => 'Pedido creado con éxito', 'pedido' => $pedido->load('detalles')], 201);
        });
    }

    // Ver detalle de un pedido específico
    public function show($id)
    {
        $pedido = Pedido::with(['detalles.producto'])->find($id);
        if (!$pedido) return response()->json(['message' => 'Pedido no encontrado'], 404);
        return response()->json($pedido, 200);
    }

    // --- FUNCIÓN AGREGADA PARA EL MÉTODO PUT ---
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $pedido = Pedido::find($id);

            if (!$pedido) {
                return response()->json(['message' => 'Pedido no encontrado'], 404);
            }

            // Actualizar datos generales
            $pedido->update([
                'id_usuario' => $request->id_usuario,
                'id_comercio' => $request->id_comercio,
                'total_pagado' => $request->total_pagado,
                'estado' => $request->estado ?? $pedido->estado
            ]);

            // Actualizar productos: Borramos los anteriores y registramos los nuevos
            DetallePedido::where('id_pedido', $id)->delete();

            foreach ($request->productos as $item) {
                DetallePedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);
            }

            return response()->json(['message' => 'Pedido actualizado con éxito', 'pedido' => $pedido->load('detalles')], 200);
        });
    }

    // Cancelar pedido
    public function destroy($id)
    {
        $pedido = Pedido::find($id);
        if (!$pedido) return response()->json(['message' => 'Pedido no encontrado'], 404);
        
        $pedido->update(['estado' => 'cancelado']);
        return response()->json(['message' => 'Pedido cancelado'], 200);
    }
}