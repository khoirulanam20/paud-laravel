<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('monev:generate')->monthlyOn(1, '02:00');
        $schedule->command('monev:finalize-stale')->hourly();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'admin.menu' => \App\Http\Middleware\EnsureAdminMenuAccess::class,
            'lembaga.sekolah' => \App\Http\Middleware\EnsureLembagaSekolahContext::class,
            'admin.activity' => \App\Http\Middleware\EnableAdminActivityLogging::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function ($response, $e) {
            if ($response->getStatusCode() === 419) {
                return redirect()->route('login')->with('warning', 'Sesi Anda telah berakhir, silakan login kembali.');
            }
            return $response;
        });
    })->create();
