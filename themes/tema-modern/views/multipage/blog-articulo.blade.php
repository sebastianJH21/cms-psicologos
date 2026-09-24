@extends('theme::layout')
@section('titulo', $articulo->meta_title ?: $articulo->titulo)
@section('descripcion', $articulo->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? ''), 160))

@section('contenido')
<article class="t-mod__article">
    <div class="t-mod__container t-mod__container--narrow">
        <header class="t-mod__article-head">
            @if ($articulo->categoria)
                <a href="{{ url('/blog?categoria='.$articulo->categoria->slug) }}" class="t-mod__article-cat">{{ $articulo->categoria->nombre }}</a>
            @endif
            <h1>{{ $articulo->titulo }}</h1>
            <p class="t-mod__article-date"><i class="fa-regular fa-calendar"></i> {{ $articulo->published_at?->translatedFormat('d \d\e F \d\e Y') }}</p>
        </header>
        @if ($articulo->imagen_path)<div class="t-mod__article-image"><img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}"></div>@endif
        <div class="t-mod__article-body wysiwyg">{!! $articulo->contenido !!}</div>
        <a href="{{ url('/blog') }}" class="t-mod__btn t-mod__btn--ghost"><i class="fa-solid fa-arrow-left"></i> Volver al blog</a>
    </div>
</article>
@endsection
