@extends('layouts.app')

@section('title', 'Noticias')

@section('content')
<section class="bg-pattern pt-32 pb-16 text-white">
    <div class="container-custom">
        <p class="text-sm font-semibold uppercase tracking-wide text-coral-200">{{ __('portal.news_eyebrow') }}</p>
        <h1 class="mt-3 font-display text-4xl font-black sm:text-5xl">{{ __('portal.news_title') }}</h1>
        <p class="mt-4 max-w-2xl text-white/80">{{ __('portal.news_intro') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="container-custom">
        <div class="mb-10 flex flex-col gap-5 rounded-3xl border border-red-100 bg-white p-5 shadow-lg shadow-red-950/5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div>
                <p class="text-xs font-black uppercase tracking-[.18em] text-red-700">{{ __('portal.weekly_publications') }}</p>
                <h2 class="mt-2 text-2xl font-black text-gray-950">
                    {{ __('portal.week_range', ['from' => $weekStart->format('d/m/Y'), 'to' => $weekEnd->format('d/m/Y')]) }}
                </h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a class="inline-flex min-h-11 items-center gap-2 rounded-full border border-red-200 px-4 py-2 text-sm font-black text-red-900 hover:bg-red-50" href="{{ route('noticias', ['semana' => $previousWeek]) }}">
                    <i class="fa-solid fa-arrow-left"></i>{{ __('portal.previous_week') }}
                </a>
                @unless($isCurrentWeek)
                    <a class="inline-flex min-h-11 items-center rounded-full bg-red-900 px-4 py-2 text-sm font-black text-white hover:bg-red-700" href="{{ route('noticias') }}">{{ __('portal.current_week') }}</a>
                @endunless
                <a class="inline-flex min-h-11 items-center gap-2 rounded-full border border-red-200 px-4 py-2 text-sm font-black text-red-900 hover:bg-red-50" href="{{ route('noticias', ['semana' => $nextWeek]) }}">
                    {{ __('portal.next_week') }}<i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
            @forelse($noticias as $noticia)
                <article class="content-card">
                    <img class="h-56 w-full object-cover" src="{{ $noticia->imagen_url }}" alt="{{ $noticia->titulo }}">
                    <div class="p-6">
                        <p class="text-sm font-semibold text-coral-700">{{ optional($noticia->publicado_en)->format('d/m/Y') }}</p>
                        <h2 class="mt-3 text-2xl font-bold">{{ $noticia->titulo }}</h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ $noticia->resumen }}</p>
                        <a class="mt-5 inline-flex font-semibold text-ocean-700" href="{{ route('noticias.show', $noticia) }}">{{ __('portal.read_news') }} <i class="fa-solid fa-arrow-right ml-2 mt-1"></i></a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-8 shadow-sm md:col-span-3">{{ __('portal.no_news_week') }}</div>
            @endforelse
        </div>
        <div class="mt-10">{{ $noticias->links() }}</div>

    </div>
</section>
@endsection
