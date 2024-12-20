<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

// Album //

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', AlbumController::class)->parameters(['' => 'album']);
    Route::get('/by-title/{title}', [AlbumController::class, 'getAlbumByTitle']);
    Route::get('/by-user-id/{userId}', [AlbumController::class, 'getAlbumsByUserId']);
    Route::get('/albums/recents', [AlbumController::class, 'getRecentlyAlbums']);
    Route::get('/albums/by-my-country', [AlbumController::class, 'getAlbumsByMyCountry']);
});
