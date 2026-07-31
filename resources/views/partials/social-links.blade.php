@php($socialLinks = $siteIdentity?->socialLinks() ?? [])
@if(count($socialLinks))
    <nav class="global-social-links" aria-label="Redes sociales oficiales">
        @foreach($socialLinks as $social)
            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Abrir {{ $social['label'] }}" title="{{ $social['label'] }}">
                <i class="fa-brands {{ $social['icon'] }}" aria-hidden="true"></i>
                <span class="sr-only">{{ $social['label'] }}</span>
            </a>
        @endforeach
    </nav>
@endif
