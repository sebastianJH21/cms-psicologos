@php
    $articulos = $articulos ?? \App\Models\Articulo::publicados()->orderByDesc('published_at')->limit(3)->get();
    $isLanding = ($themeMode ?? 'landing') === 'landing';
@endphp
@if ($articulos->isNotEmpty())
<section class="t-mod__section" id="blog">
    <div class="t-mod__container">
        <div class="t-mod__sec-head">
            <span class="t-mod__overline">{{ phrase('blog_overline', 'Blog') }}</span>
            <h2 class="t-mod__h2">{{ phrase('blog_title', 'Lecturas recomendadas') }}</h2>
            @if (phrase('blog_description'))<p>{{ phrase('blog_description') }}</p>@endif
        </div>
        <div class="t-mod__posts">
            @foreach ($articulos as $articulo)
                <a href="{{ url('/blog/' . $articulo->slug) }}" class="t-mod__post">
                    <div class="t-mod__post-img">
                        @if ($articulo->imagen_path)
                            <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
                        @else
                            <img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $articulo->titulo }}">
                        @endif
                    </div>
                    <div class="t-mod__post-body">
                        @if ($articulo->categoria)<span class="t-mod__post-cat">{{ $articulo->categoria->nombre }}</span>@endif
                        <h3>{{ $articulo->titulo }}</h3>
                        <p class="t-mod__post-date">{{ $articulo->published_at?->translatedFormat('d M Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        @if ($isLanding)
            <div class="t-mod__blog-more">
                <a href="{{ url('/blog') }}" class="t-mod__btn t-mod__btn--primary">Ver todos los artículos</a>
            </div>
        @endif
    </div>
</section>
@endif
