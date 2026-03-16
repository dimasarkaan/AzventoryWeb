<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

    )
    ->withBroadcasting(__DIR__.'/../routes/channels.php')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'password.changed' => \App\Http\Middleware\EnsurePasswordIsChanged::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            \Pusher\PusherException::class,
        ]);
        
        $exceptions->reportable(function (\Throwable $e) {
            // Abaikan error "Could not resolve host" yang berkaitan dengan Pusher
            if (str_contains($e->getMessage(), 'api-ap1.pusher.com') || 
                str_contains($e->getMessage(), 'cURL error 6')) {
                return false;
            }
        });
    })->create();
