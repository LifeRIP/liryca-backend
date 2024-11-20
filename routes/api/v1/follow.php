<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FollowsController;


// Follow con middleware//

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', FollowsController::class)->parameters(['' => 'follow']);
    Route::get('/followUnit/{following_id}', [FollowsController::class, 'followUnit']);
    Route::get('/followsCount/{user_id}', [FollowsController::class, 'followsCount']);
    Route::get('/getFollowsInfo/{user_id}', [FollowsController::class, 'getFollowsInfo']);
});
