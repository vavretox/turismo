<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveProviderSession
{
    public const TIMEOUT_SECONDS = 1200;
    public const SESSION_KEY = 'provider_last_activity';

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'provider' || $user->tourismServiceProvider?->status !== 'approved') {
            return redirect()->route('prestador.login')->withErrors(['email' => 'Inicia sesión con una cuenta de prestador habilitada.']);
        }

        $lastActivity = (int) $request->session()->get(self::SESSION_KEY, now()->timestamp);
        if (now()->timestamp - $lastActivity >= self::TIMEOUT_SECONDS) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('prestador.login')->with('session_expired', 'Tu sesión se cerró por permanecer 20 minutos sin actividad.');
        }

        $request->session()->put(self::SESSION_KEY, now()->timestamp);

        return $next($request);
    }
}
