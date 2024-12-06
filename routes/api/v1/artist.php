<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;

// Artist //
Route::middleware([
    'auth:sanctum',
    //'role_or_permission' . RoleEnum::ARTIST
])->group(function () {
    Route::resource('/', ArtistController::class)->parameters(['' => 'artist']);
    Route::get('/artists/following', [ArtistController::class, 'getFollowingArtists']);
    Route::get('/artists/top', [ArtistController::class, 'getTopArtists']);
});
