<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // --- PERBAIKAN: Cukup satu kali panggil withMiddleware ---
    ->withMiddleware(function (Middleware $middleware) {
        
        // Izinkan rute ini diakses tanpa Token CSRF
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback', // <--- WAJIB ADA INI
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();