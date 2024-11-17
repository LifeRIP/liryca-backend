<?php

use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', SongController::class)->parameters(['' => 'song']);
});
