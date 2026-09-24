@extends('theme::layout')

@section('titulo', 'Blog · ' . ($user?->nombre ?? 'Psicología'))

@section('contenido')
<div class="layout__blog" id="blog">
    <div class="blog__container">
        <header class="blog__header">
            <h4 class="blog__subtitle">Blog</h4>
            <h2 class="blog__title">Artículos sobre psicología y bienestar</h2>
        </header>

        @if ($categorias->isNotEmpty())
            <div class="blog__categorias">
                <a href="{{ url('/blog') }}" class="blog__cat-btn {{ $categoriaActual ? '' : 'is-active' }}" data-ajax-cat="">Todas</a>
                @foreach ($categorias as $cat)
                    <a href="{{ url('/blog?categoria=' . $cat->slug) }}" class="blog__cat-btn {{ $categoriaActual === $cat->slug ? 'is-active' : '' }}" data-ajax-cat="{{ $cat->slug }}">{{ $cat->nombre }}</a>
                @endforeach
            </div>
        @endif

        <div id="t-base__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</div>
@endsection
