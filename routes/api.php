<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// Verificar si la función ya está declarada
if (!function_exists('loadRoutesFromVersion')) {
    // Función para cargar rutas de una versión específica
    function loadRoutesFromVersion($version)
    {
        $path = base_path("routes/api/{$version}");
        if (File::exists($path)) {
            foreach (File::allFiles($path) as $file) {
                $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                Route::prefix($filename)->group(function () use ($file) {
                    require_once $file->getPathname();
                });
            }
        }
    }
}

// Cargar rutas de la versión 1
Route::prefix('v1')->group(function () {
    loadRoutesFromVersion('v1');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
