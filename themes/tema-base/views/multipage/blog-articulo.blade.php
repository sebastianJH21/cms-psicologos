@extends('theme::layout')

@section('titulo', $articulo->meta_title ?: $articulo->titulo . ' · ' . ($user?->nombre ?? 'Blog'))
@section('descripcion', $articulo->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? ''), 160))

@section('contenido')
<article class="blog-post">
    <header class="blog-post__header">
        @if ($articulo->categoria)
            <a href="{{ url('/blog?categoria=' . $articulo->categoria->slug) }}" class="blog-post__cat">
                <i class="fa-solid fa-tag"></i> {{ $articulo->categoria->nombre }}
            </a>
        @endif
        <h1 class="blog-post__title">{{ $articulo->titulo }}</h1>
        <p class="blog-post__meta">
            <i class="fa-regular fa-calendar"></i>
            {{ $articulo->published_at?->translatedFormat('d \d\e F \d\e Y') }}
        </p>
    </header>

    @if ($articulo->imagen_path)
        <div class="blog-post__cover">
            <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
        </div>
    @endif

    <div class="blog-post__content wysiwyg">
        {!! $articulo->contenido !!}
    </div>

    <div class="blog-post__back">
        <a href="{{ url('/blog') }}" class="blog-post__back-link">
            <i class="fa-solid fa-arrow-left"></i> Volver al blog
        </a>
    </div>
</article>

@if ($relacionados->isNotEmpty())
    <div class="layout__blog blog-related">
        <div class="blog__container">
            <header class="blog__header">
                <h2 class="blog__title blog-related__title">También te podría interesar</h2>
            </header>
            <div class="blog__articles">
                @foreach ($relacionados as $rel)
                    <article class="articles__article">
                        <a href="{{ url('/blog/' . $rel->slug) }}" class="blog-related__link">
                            <div class="article__container-img">
                                <div class="article__container-date">
                                    <p class="article__date">{{ $rel->published_at?->format('d') }} <br /> {{ $rel->published_at?->translatedFormat('M') }}</p>
                                </div>
                                @if ($rel->imagen_path)
                                    <img class="article__img" src="{{ asset('storage/' . $rel->imagen_path) }}" alt="{{ $rel->titulo }}">
                                @else
                                    <img class="article__img" src="{{ theme_asset('assets/img/blog' . (($loop->index % 3) + 1) . '.jpg') }}" alt="{{ $rel->titulo }}">
                                @endif
                                <h2 class="article__title">{{ $rel->titulo }}</h2>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
@endif
@endsection
