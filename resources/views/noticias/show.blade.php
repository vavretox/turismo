@extends('layouts.app')

@section('title', $noticia->titulo)
@section('description', $noticia->resumen)

@section('content')
<section class="pt-28 pb-16">
    <div class="container-custom max-w-4xl">
        <p class="text-sm font-semibold text-coral-700">{{ optional($noticia->publicado_en)->format('d/m/Y') }}</p>
        <h1 class="mt-3 font-display text-4xl font-bold md:text-5xl">{{ $noticia->titulo }}</h1>
        @if($noticia->imagen_url)
            <img class="mt-8 aspect-video w-full rounded-md object-cover" src="{{ $noticia->imagen_url }}" alt="{{ $noticia->titulo }}">
        @endif
        <article class="prose prose-slate mt-8 max-w-none">
            <p class="lead">{{ $noticia->resumen }}</p>
            <p>{{ $noticia->contenido }}</p>
        </article>
        @if($noticia->fuente_url)
            <a
                class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#1877f2] px-5 py-3 font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-[#0f66d8]"
                href="{{ $noticia->fuente_url }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                <i class="fa-brands fa-facebook-f"></i>
                <span>
                    Ver publicación original
                    @if($noticia->fuente_nombre)
                        en {{ $noticia->fuente_nombre }}
                    @endif
                </span>
            </a>
        @endif
        @if($noticia->destino)
            <a class="mt-8 inline-flex font-semibold text-ocean-700" href="{{ route('destinos.show', $noticia->destino) }}">Relacionado: {{ $noticia->destino->nombre }}</a>
        @endif
    </div>
</section>
@endsection
