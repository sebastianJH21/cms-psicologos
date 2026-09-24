@php
    $articulos = $articulos ?? \App\Models\Articulo::publicados()->orderByDesc('published_at')->limit(3)->get();
    $blogUrl = ($themeMode ?? 'landing') === 'landing' ? '#blog' : url('/blog');
@endphp
@if ($articulos->isNotEmpty())
<section class="t-clinica__section" id="blog">
    <div class="t-clinica__container">
        <div class="t-clinica__section-header">
            <span class="t-clinica__overtitle">{{ phrase('blog_overline', 'Blog') }}</span>
            <h2 class="t-clinica__h2">{{ phrase('blog_title', 'Lecturas recomendadas') }}</h2>
        </div>
        <div class="t-clinica__blog-grid">
            @foreach ($articulos as $articulo)
                <article class="t-clinica__post">
                    <a href="{{ url('/blog/' . $articulo->slug) }}">
                        <div class="t-clinica__post-image">
                            @if ($articulo->imagen_path)
                                <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
                            @else
                                <img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $articulo->titulo }}">
                            @endif
                        </div>
                        <div class="t-clinica__post-content">
                            @if ($articulo->categoria)
                                <span class="t-clinica__post-category">{{ $articulo->categoria->nombre }}</span>
                            @endif
                            <h3>{{ $articulo->titulo }}</h3>
                            <p class="t-clinica__post-date"><i class="fa-regular fa-calendar"></i> {{ $articulo->published_at?->translatedFormat('d M Y') }}</p>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
        @if (true)
            <div class="t-clinica__center">
                <a href="{{ url('/blog') }}" class="t-clinica__btn t-clinica__btn--ghost">Ver todos los artículos</a>
            </div>
        @endif
    </div>
</section>
@endif
