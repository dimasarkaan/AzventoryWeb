<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Tangani permintaan yang masuk.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check() || ! in_array(auth()->user()->role->value, $roles)) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
        }

        if (auth()->user()->status !== 'aktif') {
            \Illuminate\Support\Facades\Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'login' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        return $next($request);
    }
}
