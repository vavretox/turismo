@php
    $fontStacks = [
        'Inter' => "'Inter', sans-serif",
        'Montserrat' => "'Montserrat', sans-serif",
        'Poppins' => "'Poppins', sans-serif",
        'Roboto' => "'Roboto', sans-serif",
        'Nunito' => "'Nunito', sans-serif",
        'Lora' => "'Lora', serif",
        'Open Sans' => "'Open Sans', sans-serif",
        'Playfair Display' => "'Playfair Display', serif",
        'Raleway' => "'Raleway', sans-serif",
        'Merriweather' => "'Merriweather', serif",
        'Oswald' => "'Oswald', sans-serif",
        'Quicksand' => "'Quicksand', sans-serif",
        'Rubik' => "'Rubik', sans-serif",
        'Ubuntu' => "'Ubuntu', sans-serif",
        'Bebas Neue' => "'Bebas Neue', sans-serif",
        'Dancing Script' => "'Dancing Script', cursive",
        'Pacifico' => "'Pacifico', cursive",
    ];
    $bodyFont = $fontStacks[$siteIdentity?->fuente_texto ?: 'Inter'] ?? $fontStacks['Inter'];
    $headingFont = $fontStacks[$siteIdentity?->fuente_titulos ?: 'Montserrat'] ?? $fontStacks['Montserrat'];
    $baseFontSize = max(12, min(24, (int) ($siteIdentity?->tamano_texto ?: 16)));
    $bodyWeight = in_array((int) $siteIdentity?->peso_texto, [300, 400, 500, 600], true) ? (int) $siteIdentity->peso_texto : 400;
    $headingWeight = in_array((int) $siteIdentity?->peso_titulos, [500, 600, 700, 800, 900], true) ? (int) $siteIdentity->peso_titulos : 800;
    $buttonWeight = in_array((int) $siteIdentity?->peso_botones, [400, 500, 600, 700, 800], true) ? (int) $siteIdentity->peso_botones : 700;
    $headingSpacing = max(-0.03, min(0.05, (float) ($siteIdentity?->espaciado_titulos ?? 0)));
@endphp
<!DOCTYPE html>
<html lang="es" data-target-locale="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Portal Turistico'))</title>
    <meta name="description" content="@yield('description', 'Portal turistico con destinos, eventos y noticias.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800;900&family=Lora:wght@400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Montserrat:wght@500;600;700;800;900&family=Nunito:wght@300;400;500;600;700;800;900&family=Open+Sans:wght@300;400;500;600;700;800&family=Oswald:wght@300;400;500;600;700&family=Pacifico&family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&family=Quicksand:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700;800;900&family=Roboto:wght@300;400;500;600;700;800;900&family=Rubik:wght@300;400;500;600;700;800;900&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --site-font-body: {{ $bodyFont }};
            --site-font-heading: {{ $headingFont }};
            --site-font-size: {{ $baseFontSize }}px;
            --site-font-weight: {{ $bodyWeight }};
            --site-heading-weight: {{ $headingWeight }};
            --site-button-weight: {{ $buttonWeight }};
            --site-heading-spacing: {{ $headingSpacing }}em;
        }
        html { font-size: var(--site-font-size); }
        body, body .font-sans { font-family: var(--site-font-body) !important; font-weight: var(--site-font-weight); }
        body h1, body h2, body h3, body h4, body h5, body h6, body .font-display {
            font-family: var(--site-font-heading) !important;
            font-weight: var(--site-heading-weight) !important;
            letter-spacing: var(--site-heading-spacing);
        }
        body button, body .btn-primary, body .btn-secondary, body .btn-outline, body a[class*="font-bold"], body a[class*="font-semibold"] {
            font-weight: var(--site-button-weight) !important;
        }
    </style>
</head>
<body>
    @include('partials.navbar')
    @if(
        request()->routeIs('prestador.*', 'prestadores.*', 'servicios.*')
        || (request()->routeIs('lugares.show') && isset($place) && $place->tourism_service_provider_id)
        || (request()->routeIs('mapa.interactivo') && request()->filled('prestador'))
    )
        @include('partials.provider-back-button')
    @endif
    <main>@yield('content')</main>
    @include('partials.footer')
    @include('partials.social-links')
    @if(request()->routeIs('home'))
        @include('partials.weekly-activity-popup')
        @include('partials.mobile-inspire-button')
    @endif
    @include('partials.tourism-widget')
    @include('partials.page-translator')
    @if(auth()->check() && auth()->user()->role === 'provider' && request()->routeIs('prestador.*'))
        @include('partials.provider-session-timeout')
    @endif
    @stack('scripts')
</body>
</html>
