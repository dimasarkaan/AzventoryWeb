<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware untuk mengecek apakah pengguna sudah melakukan aktivasi akun.
// Jika mereka belum mengganti password bawaan sistem, paksa (redirect) mereka ke halaman "Ganti Password" sebelum mengizinkan akses ke halaman lain.
class EnsurePasswordIsChanged
{
    // Tangani setiap pergerakan/klik pengguna di website
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->password_changed_at && ! $request->routeIs('password.change', 'password.change.store', 'logout', 'api.logout', 'api.me.update')) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Anda harus mengganti kata sandi default sebelum melanjutkan.',
                    'action_required' => 'password_change'
                ], 403);
            }
            return redirect()->route('password.change')->with('warning', 'Anda harus mengganti kata sandi default sebelum melanjutkan.');
        }

        return $next($request);
    }
}
