<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

//rutas para el inicio de sesión con Facebook
Route::get('/login-facebook', [AuthController::class, 'redirectToFacebook']);
Route::get('/facebook-callback', [AuthController::class, 'handleFacebookCallback']);

//rutas para el inicio de sesión con Github
Route::get('/login-github', [AuthController::class, 'redirectToGithub']);
Route::get('/github-callback', [AuthController::class, 'handleGithubCallback']);


Route::get('/routes', function () {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        $routes[] = [
            'uri' => $route->uri,
            'methods' => $route->methods,
            'action' => $route->action,
        ];
    }
    return $routes;
});
