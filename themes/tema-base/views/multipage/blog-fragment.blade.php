@if ($articulos->isEmpty())
    <div class="blog__empty">
        <i class="fa-regular fa-newspaper"></i>
        <p>Aún no hay artículos publicados.</p>
    </div>
@else
    <div class="blog__articles">
        @foreach ($articulos as $articulo)
            <article class="articles__article">
                <a href="{{ route('public.blog.show', $articulo->slug) }}" class="articles__article-link">
                    <div class="article__container-img">
                        <div class="article__container-date">
                            <p class="article__date">{{ $articulo->published_at?->format('d') }} <br /> {{ $articulo->published_at?->translatedFormat('M') }}</p>
                        </div>
                        @if ($articulo->imagen_path)
                            <img class="article__img" src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
                        @else
                            <img class="article__img" src="{{ theme_asset('assets/img/blog' . (($loop->index % 3) + 1) . '.jpg') }}" alt="{{ $articulo->titulo }}">
                        @endif
                        <h2 class="article__title">{{ $articulo->titulo }}</h2>
                    </div>

                    <div class="article__bottom-content">
                        <div class="article__comments">
                            <i class="fa-solid fa-tag comments__ico"></i>
                            <span class="comments__text">{{ $articulo->categoria?->nombre ?? 'Artículo' }}</span>
                        </div>
                        <div class="article__container-btn">
                            <span class="article__btn-more">
                                <i class="fa-solid fa-arrow-right btn-more__arrow"></i>
                                <span class="btn-more__text">Leer más</span>
                            </span>
                        </div>
                    </div>
                </a>
            </article>
        @endforeach
    </div>

    @if ($articulos->hasPages())
        <nav class="t-base__pagination" aria-label="Paginación">
            @if ($articulos->onFirstPage())
                <span class="t-base__pagination-link t-base__pagination-link--disabled"><i class="fa-solid fa-arrow-left"></i></span>
            @else
                <a class="t-base__pagination-link" href="{{ $articulos->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-arrow-left"></i></a>
            @endif

            @foreach ($articulos->getUrlRange(1, $articulos->lastPage()) as $page => $url)
                @if ($page == $articulos->currentPage())
                    <span class="t-base__pagination-link t-base__pagination-link--active">{{ $page }}</span>
                @else
                    <a class="t-base__pagination-link" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($articulos->hasMorePages())
                <a class="t-base__pagination-link" href="{{ $articulos->nextPageUrl() }}" rel="next"><i class="fa-solid fa-arrow-right"></i></a>
            @else
                <span class="t-base__pagination-link t-base__pagination-link--disabled"><i class="fa-solid fa-arrow-right"></i></span>
            @endif
        </nav>
    @endif
@endif
