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
         // GLOBAL MIDDLEWARE - Runs on EVERY request
        $middleware->append(\App\Http\Middleware\CheckSessionExpiration::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user.session' => \App\Http\Middleware\UserSession::class,
            'api.user.session' => \App\Http\Middleware\ApiUserSession::class,
            'verified.user' => \App\Http\Middleware\VerifiedUser::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
