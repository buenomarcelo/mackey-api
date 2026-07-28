<?php

use Illuminate\Support\Facades\Route;
use MAC\Models\Abastecimento\Controllers\AbastecimentoController;
use MAC\Models\Auth\Controllers\AuthController;
use MAC\Models\Caminhao\Controllers\CaminhaoController;
use MAC\Models\Motorista\Controllers\MotoristaController;

Route::post('auth/login', [AuthController::class, 'login'])->name('login')->middleware(['web', 'guest']);

Route::prefix('auth')->name('auth.')->middleware(['web', 'auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me', [AuthController::class, 'me'])->name('me');
});

Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::get('motoristas/{motorista}/saldo-abastecimento', [MotoristaController::class, 'saldoAbastecimento'])->name('motoristas.saldo-abastecimento');
    Route::apiResource('motoristas', MotoristaController::class);
    Route::apiResource('caminhoes', CaminhaoController::class)->parameters(['caminhoes' => 'caminhao']);
    Route::apiResource('abastecimentos', AbastecimentoController::class)->parameters(['abastecimentos' => 'abastecimento']);
});
