<?php

namespace App\Http\Controllers;

use App\Models\PlaylistSong;
use Illuminate\Support\Facades\Route;

// Playlist //

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', PlaylistSongController::class)->parameters(['' => 'playlistSong']);
    Route::get('/listOfPlaylist/{song_Id}', [PlaylistSongController::class, 'listOfPlaylist']);
    Route::post('/likeSong/{song_Id}', [PlaylistSongController::class, 'likeSong']);
    Route::get('/playlist/like', [PlaylistSongController::class, 'likeplaylist']);
});
