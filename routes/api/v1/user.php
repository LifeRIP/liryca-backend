<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('/', UserController::class)->parameters(['' => 'user']);
