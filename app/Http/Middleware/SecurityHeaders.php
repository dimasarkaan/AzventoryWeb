<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Izinkan Vite dev server (port 5173) saat development/local
        $viteDevSources = app()->environment('local')
            ? " http://127.0.0.1:5173 ws://127.0.0.1:5173"
            : "";

        // Security headers to harden the application
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Content-Security-Policy', "default-src 'self'{$viteDevSources}; worker-src 'self' blob: data:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com{$viteDevSources}; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com{$viteDevSources}; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: blob: storage: https://cdnjs.cloudflare.com; connect-src 'self' data: blob: https://unpkg.com https://cdn.jsdelivr.net wss://ws-ap1.pusher.com https://sockjs-ap1.pusher.com https://stats.pusher.com https://*.whatsapp.com{$viteDevSources};");

        return $response;
    }
}
