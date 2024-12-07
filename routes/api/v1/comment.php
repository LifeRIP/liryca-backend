<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

// Comment con middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', CommentController::class)->parameters(['' => 'comment']);
    Route::get('/post/{post_id}', [CommentController::class, 'postComments']);
});
