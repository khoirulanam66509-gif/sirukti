<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsurePasswordNotMustChange;
use App\Http\Middleware\EnsureProfileIsComplete;
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
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => CheckRole::class,
            'wajib_ganti_password' => EnsurePasswordNotMustChange::class,
            'profil.lengkap' => EnsureProfileIsComplete::class,
        ]);

        $middleware->appendToGroup('web', [
            EnsurePasswordNotMustChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
