@if($weeklyActivities->isNotEmpty())
    @php($activitySignature = $weeklyActivities->map(fn ($item) => $item->id.'-'.$item->updated_at?->timestamp)->join('_'))
    <div id="weekly-activity-popup" class="weekly-popup-backdrop" data-signature="{{ $activitySignature }}" hidden>
        <div class="weekly-popup" role="dialog" aria-modal="true" aria-labelledby="weekly-popup-title">
            <button id="weekly-popup-close" class="weekly-popup-close" type="button" aria-label="Cerrar alerta"><i class="fa-solid fa-xmark"></i></button>

            @foreach($weeklyActivities as $index => $activity)
                <article class="weekly-popup-slide" data-weekly-slide="{{ $index }}" @if($index > 0) hidden @endif>
                    <a class="weekly-popup-image" href="{{ route('actividades.show', $activity) }}" aria-label="Ver {{ $activity->titulo }}">
                        <img src="{{ $activity->imagen_url }}" alt="{{ $activity->titulo }}">
                        <span class="weekly-popup-label"><i class="fa-solid fa-bell"></i> {{ __('ui.weekly_activity') }}</span>
                    </a>
                    <div class="weekly-popup-content">
                        @if($activity->subtitulo)<p class="text-xs font-black uppercase tracking-[.18em] text-amber-600">{{ $activity->subtitulo }}</p>@endif
                        <h2 id="weekly-popup-title" class="mt-2 text-2xl font-black leading-tight text-white md:text-3xl">{{ $activity->titulo }}</h2>
                        <p class="mt-3 text-sm leading-6 text-white/80">{{ $activity->descripcion }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold text-white">
                            @if($activity->fecha_actividad)<span class="weekly-popup-chip"><i class="fa-solid fa-calendar-day"></i>{{ $activity->fecha_actividad->format('d/m/Y · H:i') }}</span>@endif
                            @if($activity->lugar)<span class="weekly-popup-chip"><i class="fa-solid fa-location-dot"></i>{{ $activity->lugar }}</span>@endif
                        </div>
                        <a class="weekly-popup-action" href="{{ route('actividades.show', $activity) }}">{{ __('ui.weekly_info') }} <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            @endforeach

            @if($weeklyActivities->count() > 1)
                <div class="weekly-popup-navigation">
                    <button type="button" data-weekly-prev aria-label="{{ __('ui.previous_activity') }}"><i class="fa-solid fa-chevron-left"></i></button>
                    <span><strong id="weekly-current">1</strong> / {{ $weeklyActivities->count() }}</span>
                    <button type="button" data-weekly-next aria-label="{{ __('ui.next_activity') }}"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            @endif
            <label class="weekly-popup-dismiss"><input id="weekly-popup-session" type="checkbox"> {{ __('ui.hide_visit') }}</label>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('weekly-activity-popup');
        if (!popup) return;
        const storageKey = 'weekly-popup-' + popup.dataset.signature;
        if (sessionStorage.getItem(storageKey) === 'dismissed') return;

        const slides = Array.from(popup.querySelectorAll('[data-weekly-slide]'));
        let current = 0;
        let autoplay = null;
        const show = function (index) {
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, position) { slide.hidden = position !== current; });
            const indicator = document.getElementById('weekly-current');
            if (indicator) indicator.textContent = current + 1;
        };
        const close = function () {
            if (autoplay) clearInterval(autoplay);
            if (document.getElementById('weekly-popup-session').checked) sessionStorage.setItem(storageKey, 'dismissed');
            document.documentElement.classList.remove('overflow-hidden');
            popup.classList.remove('is-visible');
            setTimeout(function () { popup.hidden = true; }, 220);
        };

        document.getElementById('weekly-popup-close').addEventListener('click', close);
        popup.querySelector('[data-weekly-prev]')?.addEventListener('click', function () { show(current - 1); });
        popup.querySelector('[data-weekly-next]')?.addEventListener('click', function () { show(current + 1); });
        popup.addEventListener('click', function (event) { if (event.target === popup) close(); });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !popup.hidden) close(); });

        setTimeout(function () {
            popup.hidden = false;
            document.documentElement.classList.add('overflow-hidden');
            requestAnimationFrame(function () { popup.classList.add('is-visible'); });
            if (slides.length > 1) autoplay = setInterval(function () { show(current + 1); }, 20000);
        }, 650);
    });
    </script>
@endif
