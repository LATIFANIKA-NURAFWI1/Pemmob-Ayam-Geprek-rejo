<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',   // ← Daftarkan API routes (prefix /api otomatis)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (untuk ngrok, Cloudflare Tunnel, dll)
        $middleware->trustProxies(at: '*');

        // Bypass halaman peringatan interstitial ngrok di browser HP
        $middleware->append(\App\Http\Middleware\BypassNgrokWarning::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        // Sanctum middleware untuk stateful API (opsional — hanya untuk SPA)
        // $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
