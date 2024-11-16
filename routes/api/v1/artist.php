<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum'
])->group(function () {
    Route::resource('/', UserController::class)->parameters(['' => 'artist']);
});

Route::get('/{artistId}/albums', [ArtistController::class, 'getAlbumsByArtist']);
