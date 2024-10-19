<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::middleware('auth:sanctum')->group(function () {
    Route::get('playlistsong', [PlaylistSongController::class, 'index']);
    Route::get('playlistsong/{id}', [PlaylistSongController::class, 'show']);
    Route::post('playlistsong', [PlaylistSongController::class, 'store']);
    Route::delete('playlistsong/{id}', [PlaylistSongController::class, 'destroy']);
});
