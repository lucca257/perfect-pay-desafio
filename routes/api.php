<?php

use App\Http\Controllers\TransacaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('transacoes', TransacaoController::class);
Route::post('transacoes/webhook/{provider}', [TransacaoController::class, 'webhook'])->name('transacoes.webhook');
