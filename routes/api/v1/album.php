<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

// Album //

Route::resource('/', AlbumController::class)->parameters(['' => 'album']);
