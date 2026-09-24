@if (($features['blog'] ?? true) && isset($articulos) && $articulos->isNotEmpty())
@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
@endphp
<div class="layout__blog" id="blog">
    <div class="blog__container">
        <header class="blog__header">
            <h4 class="blog__subtitle">{{ phrase('blog_overline', 'Desde el blog') }}</h4>
            <h2 class="blog__title">{{ phrase('blog_title', 'Últimos artículos') }}</h2>
            @if (phrase('blog_description'))
                <p class="blog__description">{{ phrase('blog_description') }}</p>
            @endif
        </header>

        <div class="blog__articles">
            @foreach ($articulos->take(3) as $articulo)
                <article class="articles__article">
                    <div class="article__container-img">
                        <div class="article__container-date">
                            <p class="article__date">{{ $articulo->published_at?->format('d') }} <br /> {{ $articulo->published_at?->translatedFormat('M') }}</p>
                        </div>
                        @if ($articulo->imagen_path)
                            <img class="article__img" src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
                        @else
                            <img class="article__img" src="{{ theme_asset('assets/img/blog' . ($loop->index + 1) . '.jpg') }}" alt="{{ $articulo->titulo }}">
                        @endif
                        <h2 class="article__title">{{ $articulo->titulo }}</h2>
                    </div>

                    <div class="article__bottom-content">
                        <div class="article__comments">
                            <i class="fa-regular fa-newspaper comments__ico"></i>
                            <span class="comments__text">{{ $articulo->categoria?->nombre ?? 'Artículo' }}</span>
                        </div>
                        <div class="article__container-btn">
                            <a href="{{ url('/blog/' . $articulo->slug) }}" class="article__btn-more" style="text-decoration: none;">
                                <i class="fa-solid fa-arrow-right btn-more__arrow"></i>
                                <span class="btn-more__text">Leer más</span>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($isLanding)
            <div class="blog__more-wrapper">
                <a href="{{ url('/blog') }}" class="blog__more-btn">
                    <i class="fa-solid fa-newspaper"></i>
                    <span>Ver todos los artículos</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endif
