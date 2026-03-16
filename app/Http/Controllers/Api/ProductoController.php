<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{

    public function index()
    {
        $productos = Producto::with('comercio')->get();

        return response()->json([
            "status"=>true,
            "data"=>$productos
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_comercio' => 'required|exists:comercios,id_comercio',
            'nombre' => 'required|max:150',
            'precio_original' => 'required|numeric',
            'precio_descuento' => 'required|numeric',
            'cantidad_disponible' => 'required|integer',
            'fecha_caducidad' => 'required|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $rutaFoto = null;

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('productos','public');
        }

        $producto = Producto::create([
            'id_comercio'=>$request->id_comercio,
            'id_categoria'=>$request->id_categoria,
            'nombre'=>$request->nombre,
            'descripcion'=>$request->descripcion,
            'precio_original'=>$request->precio_original,
            'precio_descuento'=>$request->precio_descuento,
            'cantidad_disponible'=>$request->cantidad_disponible,
            'fecha_caducidad'=>$request->fecha_caducidad,
            'hora_recogida_inicio'=>$request->hora_recogida_inicio,
            'hora_recogida_fin'=>$request->hora_recogida_fin,
            'activo'=>$request->activo,
            'foto'=>$rutaFoto
        ]);

        return response()->json([
            "status"=>true,
            "mensaje"=>"Producto creado correctamente",
            "data"=>$producto
        ],201);
    }

    public function show($id)
    {
        $producto = Producto::find($id);

        if(!$producto){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Producto no encontrado"
            ],404);
        }

        return response()->json([
            "status"=>true,
            "data"=>$producto
        ]);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if(!$producto){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Producto no encontrado"
            ],404);
        }

        $request->validate([
            'nombre' => 'required|max:150',
            'precio_original' => 'required|numeric'
        ]);

        if ($request->hasFile('foto')) {

            if($producto->foto){
                Storage::disk('public')->delete($producto->foto);
            }

            $producto->foto = $request->file('foto')->store('productos','public');
        }

        $producto->update($request->all());

        return response()->json([
            "status"=>true,
            "mensaje"=>"Producto actualizado",
            "data"=>$producto
        ]);
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);

        if(!$producto){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Producto no encontrado"
            ],404);
        }

        if($producto->foto){
            Storage::disk('public')->delete($producto->foto);
        }

        $producto->delete();

        return response()->json([
            "status"=>true,
            "mensaje"=>"Producto eliminado correctamente"
        ]);
    }
}