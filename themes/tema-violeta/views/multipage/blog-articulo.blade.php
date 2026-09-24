@extends('theme::layout')
@section('titulo', $articulo->meta_title ?: $articulo->titulo)
@section('descripcion', $articulo->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? ''), 160))

@section('contenido')
<article class="t-violeta__article">
    <div class="t-violeta__container t-violeta__container--narrow">
        <header class="t-violeta__article-header">
            @if ($articulo->categoria)
                <a href="{{ url('/blog?categoria='.$articulo->categoria->slug) }}" class="t-violeta__article-cat">{{ $articulo->categoria->nombre }}</a>
            @endif
            <h1>{{ $articulo->titulo }}</h1>
            <p class="t-violeta__article-date"><i class="fa-regular fa-calendar"></i> {{ $articulo->published_at?->translatedFormat('d \d\e F \d\e Y') }}</p>
        </header>

        @if ($articulo->imagen_path)
            <div class="t-violeta__article-image">
                <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
            </div>
        @endif

        <div class="t-violeta__article-body wysiwyg">{!! $articulo->contenido !!}</div>

        <a href="{{ url('/blog') }}" class="t-violeta__btn t-violeta__btn--ghost"><i class="fa-solid fa-arrow-left"></i> Volver al blog</a>
    </div>
</article>

@if ($relacionados->isNotEmpty())
    <section class="t-violeta__section t-violeta__section--alt">
        <div class="t-violeta__container">
            <div class="t-violeta__section-header"><h2 class="t-violeta__h2">También te podría interesar</h2></div>
            <div class="t-violeta__blog-grid">
                @foreach ($relacionados as $rel)
                    <article class="t-violeta__post">
                        <a href="{{ route('public.blog.show', $rel->slug) }}">
                            <div class="t-violeta__post-image">
                                @if ($rel->imagen_path)
                                    <img src="{{ asset('storage/' . $rel->imagen_path) }}" alt="{{ $rel->titulo }}">
                                @else
                                    <img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $rel->titulo }}">
                                @endif
                            </div>
                            <div class="t-violeta__post-content"><h3>{{ $rel->titulo }}</h3></div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
