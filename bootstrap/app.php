<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsurePasswordNotMustChange;
use App\Http\Middleware\EnsureProfileIsComplete;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return tap(Application::configure(basePath: dirname(__DIR__))
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
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create(), function (Application $app) {
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || str_starts_with(__DIR__, '/var/task')) {
            $tmpStorage = '/tmp/storage';
            foreach ([
                $tmpStorage.'/framework/views',
                $tmpStorage.'/framework/cache/data',
                $tmpStorage.'/framework/sessions',
                $tmpStorage.'/logs',
                '/tmp/fonts',
            ] as $dir) {
                if (! is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
            }
            $app->useStoragePath($tmpStorage);
        }
    });
