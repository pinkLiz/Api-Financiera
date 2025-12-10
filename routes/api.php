<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\TransaccionesController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\ConsejosController;
use App\Http\Controllers\Api\DashboardController;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user/profile', [UserController::class, 'userProfile']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/user/password/{id}', [UserController::class, 'updatePassword']);

    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
    Route::get('/consejos', [ConsejosController::class, 'index']);

    Route::get('/configuracion', [ConfiguracionController::class, 'show']);
    Route::put('/configuracion', [ConfiguracionController::class, 'update']);

    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::get('/categorias/{id}', [CategoriaController::class, 'show']);
    Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

    Route::get('/transacciones', [TransaccionesController::class, 'index']);
    Route::post('/transacciones', [TransaccionesController::class, 'store']);
    Route::get('/transacciones/{transaccion}', [TransaccionesController::class, 'show']);
    Route::put('/transacciones/{transaccion}', [TransaccionesController::class, 'update']);
    Route::delete('/transacciones/{transaccion}', [TransaccionesController::class, 'destroy']);
});
