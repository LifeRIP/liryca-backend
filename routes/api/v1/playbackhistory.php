<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaybackHistoryController;

// PlaybackHistory con middleware//

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/add_playback', [PlaybackHistoryController::class, 'store']);
    Route::get('/get_playbacks', [PlaybackHistoryController::class, 'show']);
});
