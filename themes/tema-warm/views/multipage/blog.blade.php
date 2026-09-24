@extends('theme::layout')
@section('titulo', 'Blog · ' . ($user?->nombre ?? ''))
@section('contenido')
<section class="t-warm__section">
    <div class="t-warm__container">
        <div class="t-warm__sec-head"><span class="t-warm__overline">— Blog</span><h2 class="t-warm__h2">Lecturas para ti</h2></div>

        @if ($categorias->isNotEmpty())
            <div class="t-warm__filters">
                <a href="{{ url('/blog') }}" class="t-warm__filter {{ !$categoriaActual ? 'is-active' : '' }}" data-ajax-cat="">Todas</a>
                @foreach ($categorias as $cat)
                    <a href="{{ url('/blog?categoria='.$cat->slug) }}" class="t-warm__filter {{ $categoriaActual === $cat->slug ? 'is-active' : '' }}" data-ajax-cat="{{ $cat->slug }}">{{ $cat->nombre }}</a>
                @endforeach
            </div>
        @endif

        <div id="t-warm__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</section>
@endsection
