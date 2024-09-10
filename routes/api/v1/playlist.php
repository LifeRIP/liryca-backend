<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::resource('/', PlaylistController::class)->parameters(['' => 'playlist']);
