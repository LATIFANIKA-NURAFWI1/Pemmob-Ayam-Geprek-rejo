<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bypass halaman peringatan interstitial ngrok untuk semua request.
 * Tanpa header ini, ngrok free tier menampilkan halaman warning di browser HP
 * yang sering menyebabkan redirect ke "about:blank".
 *
 * @see https://ngrok.com/docs/http/#rewriting-host-headers
 */
class BypassNgrokWarning
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Header ini memberitahu ngrok untuk melewati halaman peringatan browser
        $response->headers->set('ngrok-skip-browser-warning', '1');

        return $response;
    }
}
