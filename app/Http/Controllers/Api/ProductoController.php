<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{

    public function index()
    {
        $productos = Producto::with('comercio')->get();
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $producto = Producto::create($request->all());
        return response()->json($producto);
    }

    public function show($id)
    {
        $producto = Producto::find($id);
        return response()->json($producto);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if(!$producto){
            return response()->json(["mensaje"=>"Producto no encontrado"]);
        }

        $producto->update($request->all());

        return response()->json($producto);
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);

        if(!$producto){
            return response()->json(["mensaje"=>"Producto no encontrado"]);
        }

        $producto->delete();

        return response()->json(["mensaje"=>"Producto eliminado"]);
    }
}