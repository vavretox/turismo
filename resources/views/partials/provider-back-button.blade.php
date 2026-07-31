<a
    class="fixed left-4 top-24 z-40 inline-flex items-center gap-2 rounded-full border border-white/30 bg-gray-950/80 px-4 py-2.5 text-sm font-black text-white shadow-xl backdrop-blur-md transition hover:-translate-x-1 hover:bg-red-950 sm:left-6"
    href="{{ route('servicios.index') }}"
    onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }"
    aria-label="Volver a la página anterior"
>
    <i class="fa-solid fa-arrow-left"></i>
    <span>{{ __('ui.back') }}</span>
</a>
