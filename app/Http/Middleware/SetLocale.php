<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('site_locale')
            ?: $request->session()->get('locale', config('app.locale', 'es'));
        $locale = in_array($locale, ['es', 'en'], true) ? $locale : 'es';

        $request->session()->put('locale', $locale);
        App::setLocale($locale);

        return $next($request);
    }
}
