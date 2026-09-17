<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk membatasi akses berdasarkan role user.
 *
 * Cara penggunaan di routes:
 *   ->middleware('role:owner')
 *   ->middleware('role:owner,kasir')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // N9.1: Blokir staf yang dinonaktifkan oleh Owner.
        // Paksa logout agar sesi tidak bertahan.
        if (! $request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi Owner.']);
        }

        if (! in_array($request->user()->role, $roles)) {
            // Redirect ke halaman yang sesuai dengan role mereka
            $user = $request->user();

            if ($user->isKds()) {
                return redirect()->route('kds.display');
            }

            if ($user->isKasir()) {
                return redirect()->route('kasir.dashboard');
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
