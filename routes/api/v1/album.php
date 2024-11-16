<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

// Album //

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', AlbumController::class)->parameters(['' => 'album']);
});
