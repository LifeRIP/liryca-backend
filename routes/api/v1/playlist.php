<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', PlaylistController::class)->parameters(['' => 'playlist']);
});
