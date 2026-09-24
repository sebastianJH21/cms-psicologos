@extends('theme::layout')
@section('titulo', 'Blog · ' . ($user?->nombre ?? ''))

@section('contenido')
<section class="t-clinica__section">
    <div class="t-clinica__container">
        <div class="t-clinica__section-header">
            <span class="t-clinica__overtitle">Blog</span>
            <h2 class="t-clinica__h2">Artículos sobre psicología</h2>
        </div>

        @if ($categorias->isNotEmpty())
            <div class="t-clinica__filters">
                <a href="{{ url('/blog') }}" class="t-clinica__filter {{ !$categoriaActual ? 'is-active' : '' }}" data-ajax-cat="">Todas</a>
                @foreach ($categorias as $cat)
                    <a href="{{ url('/blog?categoria='.$cat->slug) }}" class="t-clinica__filter {{ $categoriaActual === $cat->slug ? 'is-active' : '' }}" data-ajax-cat="{{ $cat->slug }}">{{ $cat->nombre }}</a>
                @endforeach
            </div>
        @endif

        <div id="t-clinica__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</section>
@endsection
