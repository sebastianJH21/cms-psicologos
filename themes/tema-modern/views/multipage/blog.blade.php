@extends('theme::layout')
@section('titulo', 'Blog · ' . ($user?->nombre ?? ''))
@section('contenido')
<section class="t-mod__section">
    <div class="t-mod__container">
        <div class="t-mod__sec-head"><span class="t-mod__overline">Blog</span><h2 class="t-mod__h2">Artículos sobre <span>psicología</span></h2></div>

        @if ($categorias->isNotEmpty())
            <div class="t-mod__filters">
                <a href="{{ url('/blog') }}" class="t-mod__filter {{ !$categoriaActual ? 'is-active' : '' }}" data-ajax-cat="">Todas</a>
                @foreach ($categorias as $cat)
                    <a href="{{ url('/blog?categoria='.$cat->slug) }}" class="t-mod__filter {{ $categoriaActual === $cat->slug ? 'is-active' : '' }}" data-ajax-cat="{{ $cat->slug }}">{{ $cat->nombre }}</a>
                @endforeach
            </div>
        @endif

        <div id="t-mod__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</section>
@endsection
