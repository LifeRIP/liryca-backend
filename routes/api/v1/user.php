<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    ('role:' . RoleEnum::ADMIN->value)
])->group(function () {
    Route::resource('/', UserController::class)->parameters(['' => 'user']);
});
