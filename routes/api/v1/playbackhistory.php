<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaybackHistoryController;

// PlaybackHistory con middleware//

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/', PlaybackHistoryController::class)->parameters(['' => 'playbackhistory']);
});
