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

        // Forzamos a entero 1 o 0 para que la DB no se queje
        $activo = $request->merge(['activo' => $request->has('activo') ? 1 : 0]);

        $usuario = Usuario::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'activo' => $request->activo, 
            'password' => Hash::make($request->password),
            'foto' => $rutaFoto
        ]);

        return response()->json(["status" => true, "mensaje" => "Usuario creado", "data" => $usuario]);
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
        if (!$usuario) return response()->json(["status" => false, "mensaje" => "No existe"]);

        // QUITAMOS la validación UNIQUE estricta para que deje guardar si el email es el mismo
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        if ($request->hasFile('foto')) {
            if ($usuario->foto) Storage::disk('public')->delete($usuario->foto);
            $usuario->foto = $request->file('foto')->store('usuarios', 'public');
        }

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->telefono = $request->telefono;
        $usuario->rol = $request->rol;
        $usuario->activo = $request->has('activo') ? 1 : 0;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save(); // Usamos save() en lugar de update() para asegurar que los cambios se apliquen

        return response()->json(["status" => true, "mensaje" => "Usuario actualizado", "data" => $usuario]);
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