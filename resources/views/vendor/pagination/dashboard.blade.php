@if ($paginator->hasPages())
    <nav class="pagination-dash" aria-label="Paginación">
        <ul class="pagination-dash__list">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-dash__item pagination-dash__item--disabled">
                    <span class="pagination-dash__link" aria-hidden="true">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="pagination-dash__item">
                    <a class="pagination-dash__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Páginas --}}
            @foreach ($elements as $element)
                {{-- Separador --}}
                @if (is_string($element))
                    <li class="pagination-dash__item pagination-dash__item--separator">
                        <span class="pagination-dash__link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array de enlaces --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-dash__item pagination-dash__item--active" aria-current="page">
                                <span class="pagination-dash__link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-dash__item">
                                <a class="pagination-dash__link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-dash__item">
                    <a class="pagination-dash__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-dash__item pagination-dash__item--disabled">
                    <span class="pagination-dash__link" aria-hidden="true">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
