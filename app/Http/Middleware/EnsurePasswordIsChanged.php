<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->password_changed_at && ! $request->routeIs('password.change', 'password.change.store', 'logout')) {
            return redirect()->route('password.change')->with('warning', 'Anda harus mengganti kata sandi default sebelum melanjutkan.');
        }

        return $next($request);
    }
}
