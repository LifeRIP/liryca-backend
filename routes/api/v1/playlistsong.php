<?php

namespace App\Http\Controllers;

use App\Models\PlaylistSong;
use Illuminate\Support\Facades\Route;

// Playlist //

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', PlaylistSongController::class)->parameters(['' => 'playlistSong']);
    Route::get('/listOfPlaylist/{song_Id}', [PlaylistSongController::class, 'listOfPlaylist']);
});
