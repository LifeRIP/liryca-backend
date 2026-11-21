<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:registration');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/sendEmailRecovery', [AuthController::class, 'sendEmailRecovery'])->middleware('throttle:recovery')->name('send.email.recovery');
Route::get('/reset-password/{token}', [AuthController::class, 'recover'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'savePassword'])->middleware(['guest', 'throttle:recovery'])->name('password.update');

Route::post('/validate-token', [AuthController::class, 'validateToken'])->middleware(['auth:sanctum', 'throttle:two-factor']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendEmail'])->middleware('throttle:verification-email')->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/generate-2fa-secret', [AuthController::class, 'generate2faSecret'])->name('secret-generate');
    Route::post('/enable2fa', [AuthController::class, 'enable2fa'])->name('enable');
    Route::post('/disable-2fa', [AuthController::class, 'disable2fa'])->name('disable2fa');
    Route::post('/verify-secret', [AuthController::class, 'verify'])->middleware('throttle:two-factor')->name('verify');
    Route::put('/profile', [AuthController::class, 'updateInformation'])->name('profile.updateInformation');
});

Route::middleware(['web', 'throttle:oauth'])->group(function () {
    // Rutas para el inicio de sesión con OAuth (Google, Facebook, Github, Discord)
    Route::get('/{provider}', [AuthController::class, 'redirectToProvider'])->whereIn('provider', ['google', 'facebook', 'github', 'discord']);
    Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->whereIn('provider', ['google', 'facebook', 'github', 'discord']);
});
