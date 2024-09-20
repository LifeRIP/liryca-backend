<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // TODO: Quitar después de configurar Sanctum
        $middleware->validateCsrfTokens(except: [
            '/*', // Evitar CSRF en todas las rutas
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
