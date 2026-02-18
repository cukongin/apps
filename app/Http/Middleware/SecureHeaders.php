<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add Security Headers
        // Force HTTPS (HSTS)
        if ($request->isSecure() || app()->environment('production')) {
             $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy (CSP) - Allow Google Fonts, Analytics, etc.
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https://ui-avatars.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; connect-src 'self'; frame-src 'self' https://www.youtube.com; object-src 'none'; base-uri 'self'; form-action 'self';");

        // Permissions Policy (FKA Feature Policy)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');

        // Prevent MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referral Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Cross Domain Policies
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        return $response;
    }
}
