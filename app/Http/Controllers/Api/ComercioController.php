<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comercio;
use Illuminate\Support\Facades\Storage;

class ComercioController extends Controller
{

    public function index()
    {
        $comercios = Comercio::with('usuario')->get();

        return response()->json([
            "status"=>true,
            "data"=>$comercios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario'=>'required|exists:users,id',
            'nombre'=>'required|max:150',
            'foto'=>'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $rutaFoto=null;

        if($request->hasFile('foto')){
            $rutaFoto=$request->file('foto')->store('comercios','public');
        }

        $comercio=Comercio::create([
            'id_usuario'=>$request->id_usuario,
            'nombre'=>$request->nombre,
            'descripcion'=>$request->descripcion,
            'direccion'=>$request->direccion,
            'ciudad'=>$request->ciudad,
            'horario_apertura'=>$request->horario_apertura,
            'horario_cierre'=>$request->horario_cierre,
            'activo'=>$request->activo,
            'foto'=>$rutaFoto
        ]);

        return response()->json([
            "status"=>true,
            "mensaje"=>"Comercio creado",
            "data"=>$comercio
        ]);
    }

    public function show($id)
    {
        $comercio=Comercio::find($id);

        if(!$comercio){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Comercio no encontrado"
            ]);
        }

        return response()->json([
            "status"=>true,
            "data"=>$comercio
        ]);
    }

    public function update(Request $request,$id)
    {
        $comercio=Comercio::find($id);

        if(!$comercio){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Comercio no encontrado"
            ]);
        }

        if($request->hasFile('foto')){

            if($comercio->foto){
                Storage::disk('public')->delete($comercio->foto);
            }

            $comercio->foto=$request->file('foto')->store('comercios','public');
        }

        $comercio->update($request->all());

        return response()->json([
            "status"=>true,
            "mensaje"=>"Comercio actualizado",
            "data"=>$comercio
        ]);
    }

    public function destroy($id)
    {
        $comercio=Comercio::find($id);

        if(!$comercio){
            return response()->json([
                "status"=>false,
                "mensaje"=>"Comercio no encontrado"
            ]);
        }

        if($comercio->foto){
            Storage::disk('public')->delete($comercio->foto);
        }

        $comercio->delete();

        return response()->json([
            "status"=>true,
            "mensaje"=>"Comercio eliminado"
        ]);
    }
}