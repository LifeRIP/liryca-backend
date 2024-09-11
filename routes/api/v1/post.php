<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('/', PostController::class)->parameters(['' => 'post']);
