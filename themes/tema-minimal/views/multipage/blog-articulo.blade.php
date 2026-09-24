@extends('theme::layout')
@section('titulo', $articulo->meta_title ?: $articulo->titulo)
@section('descripcion', $articulo->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? ''), 160))

@section('contenido')
<article class="t-min__article">
    <div class="t-min__container t-min__container--narrow">
        @if ($articulo->categoria)
            <a href="{{ url('/blog?categoria='.$articulo->categoria->slug) }}" class="t-min__article-cat">{{ $articulo->categoria->nombre }}</a>
        @endif
        <h1>{{ $articulo->titulo }}</h1>
        <p class="t-min__article-date">{{ $articulo->published_at?->translatedFormat('d \d\e F \d\e Y') }}</p>

        @if ($articulo->imagen_path)
            <div class="t-min__article-image"><img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}"></div>
        @endif

        <div class="t-min__article-body wysiwyg">{!! $articulo->contenido !!}</div>

        <a href="{{ url('/blog') }}" class="t-min__article-back">← Volver al blog</a>
    </div>
</article>
@endsection
