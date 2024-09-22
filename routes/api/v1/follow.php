<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FollowsController;

// Follow con middleware//

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/add_follow', [FollowsController::class, 'store']);
    Route::get('/get_follows', [FollowsController::class, 'show']);
    Route::delete('/delete_follow', [FollowsController::class, 'destroy']);
});
