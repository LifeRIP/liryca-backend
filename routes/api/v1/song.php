<?php

use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::resource('/', SongController::class)->parameters(['' => 'song']);
