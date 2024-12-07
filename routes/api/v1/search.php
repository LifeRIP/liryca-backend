<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

// Search con middleware//

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [SearchController::class, 'search_bar']);
});
