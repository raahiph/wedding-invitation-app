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
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'guest.verified' => \App\Http\Middleware\GuestVerified::class,
            'admin.auth'     => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419) {
                if ($request->is('admin/*') || $request->is('admin')) {
                    return redirect()->route('admin.login')
                        ->withErrors(['email' => 'Your session expired. Please log in again.']);
                }
                return redirect()->route('gate.show')
                    ->withErrors(['mobile' => 'Your session expired. Please try again.']);
            }
            return $response;
        });
    })->create();
