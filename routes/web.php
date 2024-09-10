<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Incluir automáticamente todas las rutas en la carpeta routes
foreach (glob(__DIR__ . '/*.php') as $routeFile) {
    if ($routeFile !== __DIR__ . '/web.php') {
        require $routeFile;
    }
}

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', [AuthController::class, 'register']);  
Route::post('/login', [AuthController::class, 'login']);


