<?php

use App\Http\Middleware\EnableAdminActivityLogging;
use App\Http\Middleware\EnsureAdminMenuAccess;
use App\Http\Middleware\EnsureLembagaSekolahContext;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
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
        $schedule->command('monev:generate')->monthlyOn(1, '02:00')
            ->onFailure(function () {
                Log::error('Scheduled command monev:generate FAILED');
            });
        $schedule->command('monev:finalize-stale')->hourly()
            ->onFailure(function () {
                Log::error('Scheduled command monev:finalize-stale FAILED');
            });
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'admin.menu' => EnsureAdminMenuAccess::class,
            'lembaga.sekolah' => EnsureLembagaSekolahContext::class,
            'admin.activity' => EnableAdminActivityLogging::class,
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
