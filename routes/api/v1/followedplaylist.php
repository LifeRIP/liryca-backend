<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FollowedPlaylistController;

// FollowedPlaylist con middleware//
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{playlist_id}', [FollowedPlaylistController::class, 'followPlaylist']);
});
