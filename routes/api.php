<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// IMPORTACIONES DE CONTROLADORES
// ==========================================

// Autenticación
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteAuthController;

// Recursos y Funciones
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ComercioController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTAS PÚBLICAS (No requieren token)
// ==========================================

// Autenticación Original / Admin
Route::post('/login', [AuthController::class, 'login']);

// Autenticación de Clientes (Passport)
Route::post('/clientes/registro', [ClienteAuthController::class, 'register']);
Route::post('/clientes/login', [ClienteAuthController::class, 'login']);


// ==========================================
// 2. RUTAS PROTEGIDAS (Requieren token de Passport de Cliente)
// ==========================================

Route::middleware('auth:api-clientes')->group(function () {

    // --- A. Gestión de Perfil de Cliente ---
    Route::get('/clientes/perfil', [ClienteAuthController::class, 'perfil']);
    Route::post('/clientes/perfil/actualizar', [ClienteAuthController::class, 'update']); // POST para imágenes
    Route::put('/clientes/password', [ClienteAuthController::class, 'changePassword']);
    Route::post('/clientes/logout', [ClienteAuthController::class, 'logout']);

    // --- B. Gestión de Pedidos (Actividad 24) ---
    Route::get('/pedidos', [PedidoController::class, 'index']);           // Ver historial de pedidos
    Route::post('/pedidos', [PedidoController::class, 'store']);          // Crear un pedido nuevo
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);       // Ver detalle de un pedido
    Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']); // Cancelar un pedido

    // --- C. Recursos Generales (Tu Respaldo) ---
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('comercios', ComercioController::class);
    Route::apiResource('usuarios', UsuarioController::class);
    
    // Logout original
    Route::post('/logout', [AuthController::class, 'logout']); 
});