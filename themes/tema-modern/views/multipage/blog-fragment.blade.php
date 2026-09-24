@if ($articulos->isEmpty())
    <div class="t-mod__empty"><p>Aún no hay artículos publicados.</p></div>
@else
    <div class="t-mod__posts t-mod__posts--full">
        @foreach ($articulos as $articulo)
            <a href="{{ route('public.blog.show', $articulo->slug) }}" class="t-mod__post">
                <div class="t-mod__post-img">
                    @if ($articulo->imagen_path)<img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">@else<img src="{{ theme_asset('assets/img/blog-bg.jpg') }}" alt="{{ $articulo->titulo }}">@endif
                </div>
                <div class="t-mod__post-body">
                    @if ($articulo->categoria)<span class="t-mod__post-cat">{{ $articulo->categoria->nombre }}</span>@endif
                    <h3>{{ $articulo->titulo }}</h3>
                    <p class="t-mod__post-date">{{ $articulo->published_at?->translatedFormat('d M Y') }}</p>
                    @if ($articulo->extracto)<p>{{ \Illuminate\Support\Str::limit(strip_tags($articulo->extracto), 110) }}</p>@endif
                </div>
            </a>
        @endforeach
    </div>

    @if ($articulos->hasPages())
        <nav class="t-mod__pagination" aria-label="Paginación">
            @if ($articulos->onFirstPage())
                <span class="t-mod__pagination-link t-mod__pagination-link--disabled"><i class="fa-solid fa-arrow-left"></i></span>
            @else
                <a class="t-mod__pagination-link" href="{{ $articulos->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-arrow-left"></i></a>
            @endif

            @foreach ($articulos->getUrlRange(1, $articulos->lastPage()) as $page => $url)
                @if ($page == $articulos->currentPage())
                    <span class="t-mod__pagination-link t-mod__pagination-link--active">{{ $page }}</span>
                @else
                    <a class="t-mod__pagination-link" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($articulos->hasMorePages())
                <a class="t-mod__pagination-link" href="{{ $articulos->nextPageUrl() }}" rel="next"><i class="fa-solid fa-arrow-right"></i></a>
            @else
                <span class="t-mod__pagination-link t-mod__pagination-link--disabled"><i class="fa-solid fa-arrow-right"></i></span>
            @endif
        </nav>
    @endif
@endif
