<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FollowsController;


// Follow con middleware//

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', FollowsController::class)->parameters(['' => 'follow']);
    Route::get('/followUnit/{id_following}', [FollowsController::class, 'followUnit']);
});
