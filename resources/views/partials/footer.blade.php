<footer class="bg-gray-950 text-white">
    @php($brandName = $siteIdentity?->nombre ?: 'Secretaria Departamental de Turismo - GADT')
    <div class="container-custom grid gap-10 py-16 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 place-items-center text-white">
                    @if($siteIdentity?->logo_url)
                        <img class="h-12 w-12 object-contain" src="{{ $siteIdentity->logo_url }}" alt="{{ $brandName }}">
                    @else
                        <i class="fa-solid fa-landmark text-2xl text-coral-300"></i>
                    @endif
                </span>
                <h2 class="font-display text-xl font-bold leading-tight">{{ $brandName }}</h2>
            </div>
            <p class="mt-5 max-w-xl text-gray-300">{{ __('ui.footer_description') }}</p>
            @php($footerSocialLinks = $siteIdentity?->socialLinks() ?? [])
            @if(count($footerSocialLinks))
                <nav class="mt-6 flex flex-wrap gap-3" aria-label="Redes sociales en el pie de página">
                    @foreach($footerSocialLinks as $social)
                        <a class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-gray-200 transition hover:bg-ocean-700" href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}"><i class="fa-brands {{ $social['icon'] }}"></i></a>
                    @endforeach
                </nav>
            @endif
        </div>
        <div>
            <h3 class="font-semibold">{{ __('ui.explore') }}</h3>
            <div class="mt-4 grid gap-3 text-sm text-gray-300">
                <a href="{{ route('home') }}">{{ __('ui.home') }}</a>
                <a href="{{ route('destinos') }}">{{ __('ui.destinations') }}</a>
                <a href="{{ route('eventos') }}">{{ __('ui.events') }}</a>
                <a href="{{ route('noticias') }}">{{ __('ui.news') }}</a>
            </div>
        </div>
        <div>
            <h3 class="font-semibold">{{ __('ui.contact') }}</h3>
            <div class="mt-4 grid gap-3 text-sm text-gray-300">
                <p><i class="fa-solid fa-location-dot mr-2 text-coral-500"></i>Tarija, Bolivia</p>
                <p><i class="fa-solid fa-envelope mr-2 text-coral-500"></i><a class="hover:text-white" href="mailto:turismo@tarija.gob.bo">turismo@tarija.gob.bo</a></p>
                <p><i class="fa-solid fa-phone mr-2 text-coral-500"></i>+591 000 00000</p>
            </div>
        </div>
        <div>
            <h3 class="font-semibold">{{ __('ui.newsletter') }}</h3>
            <p class="mt-4 text-sm text-gray-300">{{ __('ui.newsletter_text') }}</p>
            <form class="mt-4 grid gap-3">
                <input class="rounded-lg border-white/10 bg-white/10 text-white placeholder:text-gray-400" type="email" placeholder="{{ __('ui.your_email') }}">
                <button class="rounded-lg bg-ocean-700 px-4 py-2 text-sm font-semibold hover:bg-ocean-500" type="button">{{ __('ui.subscribe') }}</button>
            </form>
        </div>
    </div>
    <div class="border-t border-white/10 bg-white/[.035]">
        <div class="container-custom flex flex-col items-center justify-between gap-5 py-7 text-center sm:flex-row sm:text-left">
            <div class="flex items-center gap-4">
                <span class="hidden h-12 w-12 shrink-0 place-items-center rounded-xl bg-white/10 text-xl text-[#eadfd2] sm:grid">
                    <i class="fa-solid fa-store"></i>
                </span>
                <div>
                    <h3 class="font-display text-lg font-black">{{ __('ui.provider_question') }}</h3>
                    <p class="mt-1 text-sm text-gray-400">{{ __('ui.provider_login_text') }}</p>
                </div>
            </div>
            <a class="inline-flex shrink-0 items-center justify-center rounded-xl bg-ocean-700 px-6 py-3 font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-ocean-500" href="{{ route('prestador.login') }}" target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-right-to-bracket mr-2"></i>{{ __('ui.login') }}
            </a>
        </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-sm text-gray-400">
        <p>&copy; {{ date('Y') }} {{ $brandName }}. {{ __('ui.rights') }}</p>
    </div>
</footer>
