<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendEmail'])->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
});

// Rutas para el inicio de sesión con Google
Route::get('/login-google', [AuthController::class, 'redirectToGoogle']);
Route::get('/google-callback', [AuthController::class, 'handleGoogleCallback']);

//rutas para el inicio de sesión con Facebook
Route::get('/login-facebook', [AuthController::class, 'redirectToFacebook']);
Route::get('/facebook-callback', [AuthController::class, 'handleFacebookCallback']);




