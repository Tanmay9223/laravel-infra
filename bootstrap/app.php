<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'scopes' => \Laravel\Passport\Http\Middleware\CheckScopes::class,
            'scope' => \Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
            // '2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
            // 'check.status' => App\Http\Middleware\CheckStaffStatus::class,
            'password.expiration' => \App\Http\Middleware\CheckPasswordExpiration::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // dd('hello');
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'status' => false,
                'message' => 'Token expired or not provided.', // Customize message as needed
            ], 401);
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            return response()->json([
                'status' => false,
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            return response()->json([
                'status' => false,
                'message' => 'Service unavailable or endpoint not found.',
            ], $e->getStatusCode() ?? 503);
        });
    })
    ->create();
