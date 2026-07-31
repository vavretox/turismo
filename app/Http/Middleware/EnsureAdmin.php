<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next, ?string $section = null): Response
    {
        if (! auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = auth()->user();
        abort_unless($user->canAccessPanel(Filament::getPanel('admin')), 403);
        abort_unless(
            $user->isAdministrator() || ($section && $user->canAccessAdminSection($section)),
            403,
        );

        return $next($request);
    }
}
