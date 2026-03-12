<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ComercioController;
use App\Http\Controllers\Api\UsuarioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* RUTAS DE LA API */

Route::apiResource('productos', ProductoController::class);
Route::apiResource('comercios', ComercioController::class);
Route::apiResource('usuarios', UsuarioController::class);