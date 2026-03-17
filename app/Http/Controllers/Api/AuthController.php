<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        // VALIDACIÓN
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = Usuario::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Credenciales incorrectas'
            ], 401);
        }

        // SOLO ADMINS (rol = master)
        if (!$user->esMaster()) {
            return response()->json([
                'error' => 'No autorizado'
            ], 403);
        }

        // TOKEN
        $token = $user->createToken('token-api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => $user
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente'
        ]);
    }
}