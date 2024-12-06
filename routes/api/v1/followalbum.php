<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FollowAlbumController;

// FollowAlbum con middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{album_id}', [FollowAlbumController::class, 'followAlbum']);
});
