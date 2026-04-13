<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    // 1. LISTAR TODOS LOS PRODUCTOS
    public function index()
    {
        $productos = Producto::with('comercio')->get();

        return response()->json([
            "status"=>true,
            "data"=>$productos
        ]);
    }

    // 2. CREAR UN PRODUCTO NUEVO
    public function store(Request $request)
    {
        // AGREGA ESTA LÍNEA AQUÍ
    \Log::info('Datos recibidos en API:', $request->all());
    \Log::info('¿Tiene archivo?: ' . ($request->hasFile('foto') ? 'SÍ' : 'NO'));
    
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

    // 3. VER UN PRODUCTO ESPECÍFICO
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

    // 4. ACTUALIZAR UN PRODUCTO (CORREGIDO)
  public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|string|max:150',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $data = $request->except(['foto', '_token', '_method']);
    $data['activo'] = $request->has('activo') ? 1 : 0;

    $http = \Illuminate\Support\Facades\Http::withToken(session('api_token'))->asMultipart();

    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');
        $http = $http->attach('foto', file_get_contents($foto->getPathname()), $foto->getClientOriginalName());
    }

    // Usamos POST con _method=PUT porque multipart no soporta PUT nativamente
    $data['_method'] = 'PUT'; 
    $response = $http->post("http://127.0.0.1:8000/api/productos/{$id}", $data);

    if ($response->successful()) {
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    return back()->with('error', 'Error al actualizar: ' . $response->body());
}

    // 5. ELIMINAR UN PRODUCTO
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if(!$producto){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Producto no encontrado"
            ],404);
        }

        // Si tiene foto, la borramos del servidor
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