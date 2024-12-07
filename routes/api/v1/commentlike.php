<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentLikeController;

// CommentLike con middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{comment_id}', [CommentLikeController::class, 'like']);
});
