<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $isEmbeddedMap = $request->routeIs('mapa.interactivo') && $request->boolean('embed');
        $isDocumentPreview = $request->routeIs('prestadores.documents.preview');
        $allowsSameOriginFrame = $isEmbeddedMap || $isDocumentPreview;

        $response->headers->set('X-Frame-Options', $allowsSameOriginFrame ? 'SAMEORIGIN' : 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
        // Alpine evaluates the expressions used by x-data, x-show and event
        // bindings at runtime. Without unsafe-eval the bundle loads, but every
        // interactive component is left in its uninitialized state.
        $scriptPolicy = "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://translate.google.com https://translate.googleapis.com https://translate.googleusercontent.com https://www.gstatic.com; ";

        $secureTransportPolicy = $request->isSecure() ? '; upgrade-insecure-requests' : '';
        $frameAncestors = $allowsSameOriginFrame ? "'self'" : "'none'";

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors {$frameAncestors}; ".
            $scriptPolicy.
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://translate.google.com https://translate.googleapis.com https://translate.googleusercontent.com https://www.gstatic.com; ".
            "font-src 'self' https://fonts.gstatic.com data:; ".
            "img-src 'self' data: blob: https:; ".
            "connect-src 'self' https:; ".
            "frame-src 'self' https://www.openstreetmap.org https://www.google.com https://maps.google.com https://translate.google.com https://translate.googleapis.com https://translate.googleusercontent.com; ".
            "object-src 'none'{$secureTransportPolicy}",
        );

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
