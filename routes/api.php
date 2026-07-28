<?php

use Illuminate\Support\Facades\Route;
use MAC\Models\Abastecimento\Controllers\AbastecimentoController;
use MAC\Models\Auth\Controllers\AuthController;
use MAC\Models\Balancete\Controllers\BalanceteController;
use MAC\Models\Caminhao\Controllers\CaminhaoController;
use MAC\Models\ContaPagarMotorista\Controllers\ContaPagarMotoristaController;
use MAC\Models\Dashboard\Controllers\DashboardController;
use MAC\Models\DespesaCaminhao\Controllers\DespesaCaminhaoController;
use MAC\Models\Motorista\Controllers\MotoristaController;
use MAC\Models\Viagem\Controllers\ViagemController;

Route::post('auth/login', [AuthController::class, 'login'])->name('login')->middleware(['web', 'guest']);

Route::middleware(['web', 'guest'])->group(function () {
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password')->middleware('throttle:3,1');
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password')->middleware('throttle:10,1');
});

Route::prefix('auth')->name('auth.')->middleware(['web', 'auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me', [AuthController::class, 'me'])->name('me');
});

Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::get('motoristas/{motorista}/saldo-abastecimento', [MotoristaController::class, 'saldoAbastecimento'])->name('motoristas.saldo-abastecimento');
    Route::apiResource('motoristas', MotoristaController::class);
    Route::apiResource('caminhoes', CaminhaoController::class)->parameters(['caminhoes' => 'caminhao']);
    Route::apiResource('viagens', ViagemController::class)->parameters(['viagens' => 'viagem']);
    Route::apiResource('abastecimentos', AbastecimentoController::class)->parameters(['abastecimentos' => 'abastecimento']);
    Route::apiResource('despesas', DespesaCaminhaoController::class)->parameters(['despesas' => 'despesaCaminhao']);

    Route::prefix('contas-pagar')->name('contas-pagar.')->group(function () {
        Route::get('/', [ContaPagarMotoristaController::class, 'index'])->name('index');
        Route::get('/resumo', [ContaPagarMotoristaController::class, 'resumo'])->name('resumo');
        Route::get('/{contaPagarMotorista}', [ContaPagarMotoristaController::class, 'show'])->name('show');
        Route::patch('/{contaPagarMotorista}/pagar', [ContaPagarMotoristaController::class, 'marcarPago'])->name('pagar');
        Route::get('/{contaPagarMotorista}/recibo', [ContaPagarMotoristaController::class, 'recibo'])->name('recibo');
    });

    Route::get('balancete', [BalanceteController::class, 'index'])->name('balancete');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
