@php
    $articulos = $articulos ?? \App\Models\Articulo::publicados()->orderByDesc('published_at')->limit(3)->get();
    $blogUrl = ($themeMode ?? 'landing') === 'landing' ? '#blog' : url('/blog');
@endphp
@if ($articulos->isNotEmpty())
<section class="t-violeta__section" id="blog">
    <div class="t-violeta__container">
        <div class="t-violeta__section-header">
            <span class="t-violeta__overtitle">{{ phrase('blog_overline', 'Blog') }}</span>
            <h2 class="t-violeta__h2">{{ phrase('blog_title', 'Lecturas recomendadas') }}</h2>
        </div>
        <div class="t-violeta__blog-grid">
            @foreach ($articulos as $articulo)
                <article class="t-violeta__post">
                    <a href="{{ url('/blog/' . $articulo->slug) }}">
                        <div class="t-violeta__post-image">
                            @if ($articulo->imagen_path)
                                <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
                            @else
                                <img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $articulo->titulo }}">
                            @endif
                        </div>
                        <div class="t-violeta__post-content">
                            @if ($articulo->categoria)
                                <span class="t-violeta__post-category">{{ $articulo->categoria->nombre }}</span>
                            @endif
                            <h3>{{ $articulo->titulo }}</h3>
                            <p class="t-violeta__post-date"><i class="fa-regular fa-calendar"></i> {{ $articulo->published_at?->translatedFormat('d M Y') }}</p>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
        @if (true)
            <div class="t-violeta__center">
                <a href="{{ url('/blog') }}" class="t-violeta__btn t-violeta__btn--ghost">Ver todos los artículos</a>
            </div>
        @endif
    </div>
</section>
@endif
