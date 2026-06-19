<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckTenant;
use App\Http\Middleware\EnsureUserIsAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // <--- FIX 1: Pastikan rute API didaftarkan di sini
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'check.tenant' => CheckTenant::class,
        ]);

        // FIX 2: Matikan proteksi CSRF khusus untuk Webhook Midtrans agar tidak terblokir eror 419
        // $middleware->validateCsrfTokens(except: [
        //     'api/midtrans/callback'
        // ]);
        $middleware->validateCsrfTokens(except: [
            'midtrans-callback'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
