<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Middleware untuk menyaring akun yang sedang terkena sanksi/dinonaktifkan.
// Jika status mereka bukan "aktif", langsung tendang (logout) mereka keluar dari sistem seketika.
class EnsureUserIsActive
{
    // Tangani setiap pergerakan/klik pengguna di website
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status !== 'aktif') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        return $next($request);
    }
}
