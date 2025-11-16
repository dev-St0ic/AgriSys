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
    ->withMiddleware(function (Middleware $middleware): void {
        // Apply security headers to all requests in production
        if (app()->environment('production')) {
            $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        }

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'verified.user' => \App\Http\Middleware\VerifiedUser::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
