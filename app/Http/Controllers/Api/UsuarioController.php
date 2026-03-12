<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{

    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        $usuario = Usuario::create($request->all());
        return response()->json($usuario);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if(!$usuario){
            return response()->json(["mensaje"=>"Usuario no encontrado"]);
        }

        $usuario->update($request->all());

        return response()->json($usuario);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if(!$usuario){
            return response()->json(["mensaje"=>"Usuario no encontrado"]);
        }

        $usuario->delete();

        return response()->json(["mensaje"=>"Usuario eliminado"]);
    }
}