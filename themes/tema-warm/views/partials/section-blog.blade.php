@php
    $articulos = $articulos ?? \App\Models\Articulo::publicados()->orderByDesc('published_at')->limit(3)->get();
    $isLanding = ($themeMode ?? 'landing') === 'landing';
@endphp
@if ($articulos->isNotEmpty())
<section class="t-warm__section" id="blog">
    <div class="t-warm__container">
        <div class="t-warm__sec-head">
            <span class="t-warm__overline">— {{ phrase('blog_overline', 'Blog') }}</span>
            <h2 class="t-warm__h2">{{ phrase('blog_title', 'Lecturas para ti') }}</h2>
            @if (phrase('blog_description'))<p>{{ phrase('blog_description') }}</p>@endif
        </div>
        <div class="t-warm__posts">
            @foreach ($articulos as $articulo)
                <article class="t-warm__post">
                    <a href="{{ url('/blog/' . $articulo->slug) }}">
                        <div class="t-warm__post-img">
                            @if ($articulo->imagen_path)<img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">@else<img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $articulo->titulo }}">@endif
                        </div>
                        <div class="t-warm__post-body">
                            @if ($articulo->categoria)<span class="t-warm__post-cat">{{ $articulo->categoria->nombre }}</span>@endif
                            <h3>{{ $articulo->titulo }}</h3>
                            <p class="t-warm__post-date">{{ $articulo->published_at?->translatedFormat('d M Y') }}</p>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
        @if ($isLanding)
            <div class="t-warm__blog-more">
                <a href="{{ url('/blog') }}" class="t-warm__btn t-warm__btn--primary">
                    Ver todos los artículos <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
</section>
@endif
