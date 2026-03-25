<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json([
            "status" => true,
            "data" => $usuarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $rutaFoto = null;
        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('usuarios', 'public');
        }

        // REPARACIÓN: Si el checkbox no se marca, mandamos 1 por defecto (Activo)
        $activo = $request->has('activo') ? $request->activo : 1;

        $usuario = Usuario::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'activo' => $activo, // Usamos la variable validada
            'password' => Hash::make($request->password),
            'foto' => $rutaFoto
        ]);

        return response()->json([
            "status" => true,
            "mensaje" => "Usuario creado",
            "data" => $usuario
        ]);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json([
                "status" => false,
                "mensaje" => "Usuario no encontrado"
            ]);
        }
        return response()->json([
            "status" => true,
            "data" => $usuario
        ]);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                "status" => false,
                "mensaje" => "Usuario no encontrado"
            ]);
        }

        if ($request->hasFile('foto')) {
            if ($usuario->foto) {
                Storage::disk('public')->delete($usuario->foto);
            }
            $usuario->foto = $request->file('foto')->store('usuarios', 'public');
        }

        // REPARACIÓN PARA UPDATE: Capturamos todos los datos
        $datos = $request->all();
        
        // Si el checkbox de activo no viene en el update, lo forzamos a 0 (Inactivo)
        // porque si estamos editando y desmarcamos, queremos que se guarde como desactivado.
        $datos['activo'] = $request->has('activo') ? $request->activo : 0;

        // Si mandaron password nuevo, lo encriptamos. Si no, lo quitamos para no borrar el viejo.
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        } else {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return response()->json([
            "status" => true,
            "mensaje" => "Usuario actualizado",
            "data" => $usuario
        ]);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json([
                "status" => false,
                "mensaje" => "Usuario no encontrado"
            ]);
        }

        if ($usuario->foto) {
            Storage::disk('public')->delete($usuario->foto);
        }

        $usuario->delete();

        return response()->json([
            "status" => true,
            "mensaje" => "Usuario eliminado"
        ]);
    }
}