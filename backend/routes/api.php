<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth.bearer')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Perfis
    Route::get('/perfis',          [PerfilController::class, 'index']);
    Route::post('/perfis',         [PerfilController::class, 'store']);
    Route::get('/perfis/{perfil}', [PerfilController::class, 'show']);
    Route::put('/perfis/{perfil}', [PerfilController::class, 'update']);
    Route::delete('/perfis/{perfil}', [PerfilController::class, 'destroy']);

    // Endereços
    Route::get('/enderecos',             [EnderecoController::class, 'index']);
    Route::post('/enderecos',            [EnderecoController::class, 'store']);
    Route::get('/enderecos/{endereco}',  [EnderecoController::class, 'show']);
    Route::put('/enderecos/{endereco}',  [EnderecoController::class, 'update']);
    Route::delete('/enderecos/{endereco}', [EnderecoController::class, 'destroy']);

    // Usuários
    Route::get('/usuarios',            [UsuarioController::class, 'index']);
    Route::post('/usuarios',           [UsuarioController::class, 'store']);
    Route::get('/usuarios/{usuario}',  [UsuarioController::class, 'show']);
    Route::put('/usuarios/{usuario}',  [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
});
