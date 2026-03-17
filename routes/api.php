<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ComercioController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// LOGIN (PÚBLICO)
Route::post('/login', [AuthController::class, 'login']);

// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('comercios', ComercioController::class);
    Route::apiResource('usuarios', UsuarioController::class);
});