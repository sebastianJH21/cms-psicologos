@php
    $articulos = $articulos ?? \App\Models\Articulo::publicados()->orderByDesc('published_at')->limit(3)->get();
    $isLanding = ($themeMode ?? 'landing') === 'landing';
@endphp
@if ($articulos->isNotEmpty())
<section class="t-min__section" id="blog">
    <div class="t-min__container">
        <p class="t-min__overline">— {{ phrase('blog_overline', 'Lecturas') }}</p>
        <h2 class="t-min__h2">{{ phrase('blog_title', 'Últimos artículos') }}</h2>
        <div class="t-min__posts">
            @foreach ($articulos as $articulo)
                <a href="{{ url('/blog/' . $articulo->slug) }}" class="t-min__post">
                    <span class="t-min__post-date">{{ $articulo->published_at?->translatedFormat('d M Y') }}</span>
                    <h3>{{ $articulo->titulo }}</h3>
                    @if ($articulo->extracto)<p>{{ \Illuminate\Support\Str::limit(strip_tags($articulo->extracto), 110) }}</p>@endif
                    <span class="t-min__post-read">Leer →</span>
                </a>
            @endforeach
        </div>
        @if ($isLanding)
            <div class="t-min__blog-more">
                <a href="{{ url('/blog') }}" class="t-min__btn t-min__btn--primary">Ver todos los artículos</a>
            </div>
        @endif
    </div>
</section>
@endif
