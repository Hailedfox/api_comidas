<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comercio;

class ComercioController extends Controller
{

    public function index()
    {
        $comercios = Comercio::with('usuario')->get();
        return response()->json($comercios);
    }

    public function store(Request $request)
    {
        $comercio = Comercio::create($request->all());
        return response()->json($comercio);
    }

    public function show($id)
    {
        $comercio = Comercio::find($id);
        return response()->json($comercio);
    }

    public function update(Request $request, $id)
    {
        $comercio = Comercio::find($id);

        if(!$comercio){
            return response()->json(["mensaje"=>"Comercio no encontrado"]);
        }

        $comercio->update($request->all());

        return response()->json($comercio);
    }

    public function destroy($id)
    {
        $comercio = Comercio::find($id);

        if(!$comercio){
            return response()->json(["mensaje"=>"Comercio no encontrado"]);
        }

        $comercio->delete();

        return response()->json(["mensaje"=>"Comercio eliminado"]);
    }
}