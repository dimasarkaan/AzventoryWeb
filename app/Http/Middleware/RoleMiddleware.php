<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware pembatas wilayah berdasarkan pangkat (Role-Based Access Control).
// Mencegah Operator nyasar ke menu Admin, atau Admin mencoba mengakses halaman khusus Superadmin.
class RoleMiddleware
{
    // Tangani setiap pergerakan/klik pengguna di website
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check() || ! in_array(auth()->user()->role->value, $roles)) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
        }

        return $next($request);
    }
}
