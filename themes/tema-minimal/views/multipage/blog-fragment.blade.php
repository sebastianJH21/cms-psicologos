@if ($articulos->isEmpty())
    <p class="t-min__empty">Aún no hay artículos.</p>
@else
    <div class="t-min__posts t-min__posts--full">
        @foreach ($articulos as $articulo)
            <a href="{{ route('public.blog.show', $articulo->slug) }}" class="t-min__post">
                <span class="t-min__post-date">{{ $articulo->published_at?->translatedFormat('d M Y') }}</span>
                <h3>{{ $articulo->titulo }}</h3>
                @if ($articulo->extracto)<p>{{ \Illuminate\Support\Str::limit(strip_tags($articulo->extracto), 130) }}</p>@endif
                <span class="t-min__post-read">Leer →</span>
            </a>
        @endforeach
    </div>

    @if ($articulos->hasPages())
        <nav class="t-min__pagination" aria-label="Paginación">
            @if ($articulos->onFirstPage())
                <span class="t-min__pagination-link t-min__pagination-link--disabled"><i class="fa-solid fa-arrow-left"></i></span>
            @else
                <a class="t-min__pagination-link" href="{{ $articulos->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-arrow-left"></i></a>
            @endif

            @foreach ($articulos->getUrlRange(1, $articulos->lastPage()) as $page => $url)
                @if ($page == $articulos->currentPage())
                    <span class="t-min__pagination-link t-min__pagination-link--active">{{ $page }}</span>
                @else
                    <a class="t-min__pagination-link" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($articulos->hasMorePages())
                <a class="t-min__pagination-link" href="{{ $articulos->nextPageUrl() }}" rel="next"><i class="fa-solid fa-arrow-right"></i></a>
            @else
                <span class="t-min__pagination-link t-min__pagination-link--disabled"><i class="fa-solid fa-arrow-right"></i></span>
            @endif
        </nav>
    @endif
@endif
