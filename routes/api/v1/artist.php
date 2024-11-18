<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;

// Artist //
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', ArtistController::class)->parameters(['' => 'artist']);
});
