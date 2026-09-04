<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        // Hanya terapkan pengecekan ini jika diakses menggunakan API Token (Sanctum)
        if ($user && $user->currentAccessToken()) {
            $method = $request->method();
            $action = 'read';

            if ($method === 'POST') {
                $action = 'create';
            } elseif (in_array($method, ['PUT', 'PATCH'])) {
                $action = 'update';
            } elseif ($method === 'DELETE') {
                $action = 'delete';
            }

            $routeName = $request->route() ? $request->route()->getName() : '';

            // Handle Edge Cases
            if ($routeName === 'api.borrowings.return') {
                $action = 'update';
            } elseif ($routeName === 'api.users.reset-password') {
                $action = 'update';
            }

            $ability = "{$module}:{$action}";

            if (! $user->tokenCan($ability)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Akses Ditolak: Token API ini tidak memiliki izin '{$ability}'.",
                ], 403);
            }
        }

        return $next($request);
    }
}
