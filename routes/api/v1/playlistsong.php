<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::resource('/', PlaylistSongController::class)->parameters(['' => 'playlistsong']);
