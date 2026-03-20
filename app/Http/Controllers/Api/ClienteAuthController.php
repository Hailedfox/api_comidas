<?php

namespace App\Http\Controllers\Api; // Namespace actualizado

use App\Http\Controllers\Controller; // Importamos el Controller base
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteAuthController extends Controller
{
    // 1. Registro de cliente (POST)
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:clientes',
            'password' => 'required|string|min:6',
            'imagen_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen_perfil')) {
            // Guarda en storage/app/public/perfiles
            $rutaImagen = $request->file('imagen_perfil')->store('perfiles', 'public');
        }

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'imagen_perfil' => $rutaImagen
        ]);

        $token = $cliente->createToken('ClienteAuthToken')->accessToken;

        return response()->json(['cliente' => $cliente, 'token' => $token], 201);
    }

    // 2. Inicio de sesión (POST)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $cliente = Cliente::where('email', $request->email)->first();

        // Verificamos credenciales
        if (!$cliente || !Hash::check($request->password, $cliente->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $cliente->createToken('ClienteAuthToken')->accessToken;

        return response()->json(['cliente' => $cliente, 'token' => $token], 200);
    }

    // 3. Ver perfil (GET)
    public function perfil()
    {
        // Obtenemos al cliente usando el guard específico
        $cliente = Auth::guard('api-clientes')->user();
        return response()->json(['cliente' => $cliente], 200);
    }

    // 4. Editar perfil y gestionar imagen (PUT / POST)
    // Nota: A veces Postman tiene problemas enviando archivos (imágenes) por el método PUT. 
    // Si te falla, cambia la ruta a POST en api.php
    public function update(Request $request)
    {
        $cliente = Auth::guard('api-clientes')->user();

        $request->validate([
            'nombre' => 'sometimes|string',
            'imagen_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('imagen_perfil')) {
            // Borra la imagen vieja si tenía una
            if ($cliente->imagen_perfil) {
                Storage::disk('public')->delete($cliente->imagen_perfil);
            }
            $cliente->imagen_perfil = $request->file('imagen_perfil')->store('perfiles', 'public');
        }

        if ($request->has('nombre')) {
            $cliente->nombre = $request->nombre;
        }

        $cliente->save();

        return response()->json(['message' => 'Perfil actualizado exitosamente', 'cliente' => $cliente], 200);
    }

    // 5. Cambiar contraseña (PUT)
    public function changePassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:6'
        ]);

        $cliente = Auth::guard('api-clientes')->user();

        if (!Hash::check($request->password_actual, $cliente->password)) {
            return response()->json(['message' => 'La contraseña actual no coincide'], 400);
        }

        $cliente->password = Hash::make($request->password_nueva);
        $cliente->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
    }

    // 6. Cierre de sesión (POST)
    public function logout(Request $request)
    {
        // Revocamos el token actual
        $request->user('api-clientes')->token()->revoke();
        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }
}