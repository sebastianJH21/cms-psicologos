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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'installed' => \App\Http\Middleware\EnsureInstalled::class,
            'not_installed' => \App\Http\Middleware\EnsureNotInstalled::class,
            'guest_psicologa' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'only_local' => \App\Http\Middleware\OnlyLocal::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
