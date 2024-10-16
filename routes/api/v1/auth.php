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

// Rutas para el inicio de sesión con Google
Route::get('/login-google', [AuthController::class, 'redirectToGoogle']);
Route::get('/google-callback', [AuthController::class, 'handleGoogleCallback']);

//rutas para el inicio de sesión con Facebook
Route::get('/login-facebook', [AuthController::class, 'redirectToFacebook']);
Route::get('/facebook-callback', [AuthController::class, 'handleFacebookCallback']);

//rutas para el inicio de sesión con Github
Route::get('login-github', [AuthController::class, 'redirectToGithub']);
Route::get('github-callback', [AuthController::class, 'handleGithubCallback']);




