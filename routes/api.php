<?php

use Illuminate\Support\Facades\Route;
use MAC\Models\Auth\Controllers\AuthController;

Route::post('auth/login', [AuthController::class, 'login'])->name('login')->middleware(['web', 'guest']);

Route::prefix('auth')->name('auth.')->middleware(['web', 'auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me', [AuthController::class, 'me'])->name('me');
});
