<?php

use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', SongController::class)->parameters(['' => 'song']);
    Route::get('/top-by-artist/{artistId}', [SongController::class, 'getTopSongsByArtist']);
    Route::get('/by-album-id/{albumId}', [SongController::class, 'getSongsByAlbumId']);
    Route::get('/songs/top-today', [SongController::class, 'getTopSongsToday']);
    Route::get('/songs/top-today-by-my-country', [SongController::class, 'getTopSongsTodayByMyCountry']);
});
