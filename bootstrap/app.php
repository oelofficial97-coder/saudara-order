<?php

use App\Http\Middleware\EnsureActiveAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
        $middleware->alias(['admin.active' => EnsureActiveAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e): void {
            error_log(sprintf(
                'SAUDARA_EXCEPTION: %s: "%s" in %s:%d',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        });
    })->create();

if ($storagePath = (getenv('LARAVEL_STORAGE_PATH') ?: env('LARAVEL_STORAGE_PATH'))) {
    $app->useStoragePath($storagePath);
}

return $app;
