@extends('theme::layout')
@section('titulo', 'Blog · ' . ($user?->nombre ?? ''))

@section('contenido')
<section class="t-min__section">
    <div class="t-min__container">
        <p class="t-min__overline">— Blog</p>
        <h2 class="t-min__h2">Artículos</h2>

        @if ($categorias->isNotEmpty())
            <div class="t-min__filters">
                <a href="{{ url('/blog') }}" class="t-min__filter {{ !$categoriaActual ? 'is-active' : '' }}" data-ajax-cat="">Todas</a>
                @foreach ($categorias as $cat)
                    <a href="{{ url('/blog?categoria='.$cat->slug) }}" class="t-min__filter {{ $categoriaActual === $cat->slug ? 'is-active' : '' }}" data-ajax-cat="{{ $cat->slug }}">{{ $cat->nombre }}</a>
                @endforeach
            </div>
        @endif

        <div id="t-min__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</section>
@endsection
