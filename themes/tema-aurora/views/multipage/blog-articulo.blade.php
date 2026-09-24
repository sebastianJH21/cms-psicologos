@extends('theme::layout')
@section('titulo', $articulo->meta_title ?: $articulo->titulo)
@section('descripcion', $articulo->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? ''), 160))
@section('contenido')
<article class="t-aurora__article">
    <div class="t-aurora__container t-aurora__container--narrow">
        @if ($articulo->categoria)<a href="{{ url('/blog?categoria='.$articulo->categoria->slug) }}" class="t-aurora__article-cat">{{ $articulo->categoria->nombre }}</a>@endif
        <h1>{{ $articulo->titulo }}</h1>
        <p class="t-aurora__article-date">{{ $articulo->published_at?->translatedFormat('d \d\e F \d\e Y') }}</p>
        @if ($articulo->imagen_path)<div class="t-aurora__article-img"><img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}"></div>@endif
        <div class="t-aurora__article-body wysiwyg">{!! $articulo->contenido !!}</div>
        <a href="{{ url('/blog') }}" class="t-aurora__btn t-aurora__btn--ghost"><i class="fa-solid fa-arrow-left"></i> Volver al diario</a>
    </div>
</article>
@endsection
