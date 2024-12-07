<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostLikeController;

// PostLike con middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{post_id}', [PostLikeController::class, 'like']);
});
