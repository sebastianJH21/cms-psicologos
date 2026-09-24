@if ($articulos->isEmpty())
    <p class="t-aurora__empty">Aún no hay artículos publicados.</p>
@else
    <div class="t-aurora__posts">
        @foreach ($articulos as $articulo)
            <article class="t-aurora__post">
                <a href="{{ url('/blog/' . $articulo->slug) }}">
                    <div class="t-aurora__post-img">
                        @if ($articulo->imagen_path)<img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">@else<div class="t-aurora__post-placeholder"><i class="fa-solid fa-feather"></i></div>@endif
                    </div>
                    <div class="t-aurora__post-meta">
                        @if ($articulo->categoria)<span class="t-aurora__post-cat">{{ $articulo->categoria->nombre }}</span>@endif
                        <time>{{ $articulo->published_at?->translatedFormat('d M Y') }}</time>
                    </div>
                    <h3>{{ $articulo->titulo }}</h3>
                    <span class="t-aurora__post-cta">Leer artículo <i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </article>
        @endforeach
    </div>
    @if ($articulos->hasPages())
        <nav class="t-aurora__pagination" aria-label="Paginación">
            @if ($articulos->onFirstPage())
                <span class="t-aurora__pagination-link t-aurora__pagination-link--disabled"><i class="fa-solid fa-arrow-left"></i></span>
            @else
                <a class="t-aurora__pagination-link" href="{{ $articulos->previousPageUrl() }}" data-page="{{ $articulos->currentPage() - 1 }}" rel="prev"><i class="fa-solid fa-arrow-left"></i></a>
            @endif

            @foreach ($articulos->getUrlRange(1, $articulos->lastPage()) as $page => $url)
                @if ($page == $articulos->currentPage())
                    <span class="t-aurora__pagination-link t-aurora__pagination-link--active">{{ $page }}</span>
                @else
                    <a class="t-aurora__pagination-link" href="{{ $url }}" data-page="{{ $page }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($articulos->hasMorePages())
                <a class="t-aurora__pagination-link" href="{{ $articulos->nextPageUrl() }}" data-page="{{ $articulos->currentPage() + 1 }}" rel="next"><i class="fa-solid fa-arrow-right"></i></a>
            @else
                <span class="t-aurora__pagination-link t-aurora__pagination-link--disabled"><i class="fa-solid fa-arrow-right"></i></span>
            @endif
        </nav>
    @endif
@endif
