@extends('theme::layout')
@section('titulo', 'Blog · ' . ($user?->nombre ?? ''))

@section('contenido')
<section class="t-violeta__section">
    <div class="t-violeta__container">
        <div class="t-violeta__section-header">
            <span class="t-violeta__overtitle">Blog</span>
            <h2 class="t-violeta__h2">Artículos sobre psicología</h2>
        </div>

        @if ($categorias->isNotEmpty())
            <div class="t-violeta__filters">
                <a href="{{ url('/blog') }}" class="t-violeta__filter {{ !$categoriaActual ? 'is-active' : '' }}" data-ajax-cat="">Todas</a>
                @foreach ($categorias as $cat)
                    <a href="{{ url('/blog?categoria='.$cat->slug) }}" class="t-violeta__filter {{ $categoriaActual === $cat->slug ? 'is-active' : '' }}" data-ajax-cat="{{ $cat->slug }}">{{ $cat->nombre }}</a>
                @endforeach
            </div>
        @endif

        <div id="t-violeta__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</section>
@endsection
