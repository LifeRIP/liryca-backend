<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Post con middleware

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', PostController::class)->parameters(['' => 'post']);
});
