<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sendEmailRecovery', [AuthController::class, 'sendEmailRecovery'])->name('send.email.recovery');
Route::get('/reset-password/{token}', [AuthController::class, 'recover'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'savePassword'])->middleware('guest')->name('password.update');

Route::post('/validate-token', [AuthController::class, 'validateToken']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendEmail'])->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/generate-2fa-secret', [AuthController::class, 'generate2faSecret'])->name('secret-generate');
    Route::post('/enable2fa', [AuthController::class, 'enable2fa'])->name('enable');
    Route::post('/disable-2fa', [AuthController::class, 'disable2fa'])->name('disable2fa');
    Route::post('/verify-secret', [AuthController::class, 'verify'])->name('verify');
});

Route::middleware(['web'])->group(function () {
    // Rutas para el inicio de sesión con OAuth (Google, Facebook, Github)
    Route::get('/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
});
