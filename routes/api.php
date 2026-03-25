<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// IMPORTACIONES DE CONTROLADORES
// ==========================================
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteAuthController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ComercioController;
use App\Http\Controllers\Api\UsuarioController;
// AGREGADO: Importamos PedidoController para que 'php artisan route:list' no truene
use App\Http\Controllers\Api\PedidoController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTAS PÚBLICAS (No requieren token)
// ==========================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/clientes/registro', [ClienteAuthController::class, 'register']);
Route::post('/clientes/login', [ClienteAuthController::class, 'login']);

// CAMBIO: Movidos aquí afuera para que el Cliente (8080) pueda ver los datos
Route::apiResource('productos', ProductoController::class);
Route::apiResource('comercios', ComercioController::class);
Route::apiResource('usuarios', UsuarioController::class);


// ==========================================
// 2. RUTAS PROTEGIDAS (Requieren token de Passport)
// ==========================================
Route::middleware('auth:api-clientes')->group(function () {

    // --- A. Gestión de Perfil de Cliente ---
    Route::get('/clientes/perfil', [ClienteAuthController::class, 'perfil']);
    Route::post('/clientes/perfil/actualizar', [ClienteAuthController::class, 'update']); 
    Route::put('/clientes/password', [ClienteAuthController::class, 'changePassword']);
    Route::post('/clientes/logout', [ClienteAuthController::class, 'logout']);

    // --- B. Gestión de Pedidos ---
    // Ahora que importamos la clase arriba, esto ya no dará error
    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']);
    
    // Logout original
    Route::post('/logout', [AuthController::class, 'logout']); 
});