@extends('theme::layout')
@section('titulo', $articulo->meta_title ?: $articulo->titulo)
@section('descripcion', $articulo->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? ''), 160))

@section('contenido')
<article class="t-natural__article">
    <div class="t-natural__container t-natural__container--narrow">
        <header class="t-natural__article-header">
            @if ($articulo->categoria)
                <a href="{{ url('/blog?categoria='.$articulo->categoria->slug) }}" class="t-natural__article-cat">{{ $articulo->categoria->nombre }}</a>
            @endif
            <h1>{{ $articulo->titulo }}</h1>
            <p class="t-natural__article-date"><i class="fa-regular fa-calendar"></i> {{ $articulo->published_at?->translatedFormat('d \d\e F \d\e Y') }}</p>
        </header>

        @if ($articulo->imagen_path)
            <div class="t-natural__article-image">
                <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
            </div>
        @endif

        <div class="t-natural__article-body wysiwyg">{!! $articulo->contenido !!}</div>

        <a href="{{ url('/blog') }}" class="t-natural__btn t-natural__btn--ghost"><i class="fa-solid fa-arrow-left"></i> Volver al blog</a>
    </div>
</article>

@if ($relacionados->isNotEmpty())
    <section class="t-natural__section t-natural__section--alt">
        <div class="t-natural__container">
            <div class="t-natural__section-header"><h2 class="t-natural__h2">También te podría interesar</h2></div>
            <div class="t-natural__blog-grid">
                @foreach ($relacionados as $rel)
                    <article class="t-natural__post">
                        <a href="{{ route('public.blog.show', $rel->slug) }}">
                            <div class="t-natural__post-image">
                                @if ($rel->imagen_path)
                                    <img src="{{ asset('storage/' . $rel->imagen_path) }}" alt="{{ $rel->titulo }}">
                                @else
                                    <img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $rel->titulo }}">
                                @endif
                            </div>
                            <div class="t-natural__post-content"><h3>{{ $rel->titulo }}</h3></div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
