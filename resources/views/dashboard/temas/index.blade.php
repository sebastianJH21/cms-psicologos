@extends('dashboard.layout')

@section('titulo', 'Temas visuales')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Temas'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/temas.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-palette" aria-hidden="true"></i>
                Temas visuales
            </h1>
            <p class="page-header__subtitle">
                Elige el diseño que mejor represente tu consulta. El tema activo se aplica automáticamente en la web pública.
            </p>
        </div>
        <div class="page-header__actions">
            <a href="https://victorroblesweb.es/contacto" target="_blank" rel="noopener" class="btn btn--secondary">
                <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
                ¿Quieres un diseño personalizado? Pídemelo aquí
            </a>
        </div>
    </header>

    <div class="temas-modo-info panel">
        <div class="temas-modo-info__content">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            <div>
                <strong>Modo actual:</strong>
                <span class="badge {{ $activeMode === 'landing' ? 'badge--primary' : 'badge--info' }}">
                    {{ $activeMode === 'landing' ? 'Landing page (una sola página)' : 'Multipágina (secciones navegables)' }}
                </span>
                &nbsp;·&nbsp;
                <strong>Tema activo:</strong>
                <span class="badge badge--success">
                    {{ $themes[$activeSlug]['name'] ?? $activeSlug }}
                </span>
            </div>
        </div>
    </div>


    <div class="temas-grid">
        @foreach ($themes as $slug => $theme)
            @php $isActive = $slug === $activeSlug; @endphp
            <article class="tema-card panel {{ $isActive ? 'tema-card--active' : '' }}">
                @if ($isActive)
                    <div class="tema-card__badge-active">
                        <i class="fa-solid fa-check-circle" aria-hidden="true"></i>
                        Activo
                    </div>
                @endif

                <div class="tema-card__preview" style="background: {{ $theme['color_palette']['bg'] ?? '#f6f2f0' }}">
                    <div class="tema-card__mockup">
                        <div class="mockup__nav" style="background: {{ $theme['color_palette']['bg'] ?? '#fff' }}; border-color: {{ $theme['color_palette']['border'] ?? $theme['color_palette']['accent'] ?? '#ddd' }}">
                            <div class="mockup__logo" style="background: {{ $theme['color_palette']['primary'] }}"></div>
                            <div class="mockup__nav-links">
                                <span style="background: {{ $theme['color_palette']['text'] ?? '#333' }}"></span>
                                <span style="background: {{ $theme['color_palette']['text'] ?? '#333' }}"></span>
                                <span style="background: {{ $theme['color_palette']['primary'] }}"></span>
                            </div>
                        </div>
                        <div class="mockup__hero" style="background: {{ $theme['color_palette']['bg_alt'] ?? $theme['color_palette']['accent'] ?? '#eee' }}">
                            <div class="mockup__hero-text">
                                <div class="mockup__h1" style="background: {{ $theme['color_palette']['secondary'] }}"></div>
                                <div class="mockup__h2" style="background: {{ $theme['color_palette']['text'] ?? '#555' }}"></div>
                                <div class="mockup__btn" style="background: {{ $theme['color_palette']['primary'] }}"></div>
                            </div>
                            <div class="mockup__hero-img" style="background: {{ $theme['color_palette']['accent'] }}"></div>
                        </div>
                        <div class="mockup__section">
                            @foreach ([0,1,2] as $i)
                                <div class="mockup__card" style="background: {{ $theme['color_palette']['bg'] ?? '#fff' }}; border-top: 3px solid {{ $theme['color_palette']['primary'] }}">
                                    <div class="mockup__card-icon" style="background: {{ $theme['color_palette']['primary'] }}"></div>
                                    <div class="mockup__card-line" style="background: {{ $theme['color_palette']['text'] ?? '#333' }}"></div>
                                    <div class="mockup__card-line mockup__card-line--short" style="background: {{ $theme['color_palette']['text'] ?? '#555' }}"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mockup__palette">
                            @foreach (($theme['preview_colors'] ?? []) as $color)
                                <span class="mockup__color" style="background: {{ $color }}"></span>
                            @endforeach
                        </div>
                    </div>
                    <a
                        href="{{ url('/') }}?preview_theme={{ $slug }}&preview_mode={{ $theme['supports'][0] ?? 'landing' }}"
                        target="_blank"
                        rel="noopener"
                        class="tema-card__preview-btn"
                        aria-label="Ver previsualización de {{ $theme['name'] }} en una nueva pestaña"
                    >
                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        Previsualizar
                    </a>
                </div>

                <div class="tema-card__info">
                    <h2 class="tema-card__name">{{ $theme['name'] }}</h2>
                    <p class="tema-card__desc">{{ $theme['description'] }}</p>

                    <div class="tema-card__palette">
                        @foreach (($theme['preview_colors'] ?? []) as $color)
                            <span class="tema-card__color" style="background: {{ $color }}" title="{{ $color }}"></span>
                        @endforeach
                    </div>

                    <div class="tema-card__modes">
                        @foreach (($theme['supports'] ?? ['landing']) as $modo)
                            <span class="badge badge--outline">
                                <i class="fa-solid {{ $modo === 'landing' ? 'fa-scroll' : 'fa-window-restore' }}" aria-hidden="true"></i>
                                {{ $modo === 'landing' ? 'Landing' : 'Multipágina' }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="tema-card__actions">
                    @if ($isActive)
                        <div class="tema-card__active-controls">
                            <span class="tema-card__active-label">
                                <i class="fa-solid fa-check" aria-hidden="true"></i>
                                Tema activo en modo
                                <strong>{{ $activeMode === 'landing' ? 'landing' : 'multipágina' }}</strong>
                            </span>
                            @if (count($theme['supports']) > 1)
                                <form method="POST" action="{{ route('dashboard.temas.activar', $slug) }}" class="tema-cambiar-modo-form">
                                    @csrf
                                    <input type="hidden" name="mode" value="{{ $activeMode === 'landing' ? 'multipage' : 'landing' }}">
                                    <button type="submit" class="btn btn--secondary btn--sm">
                                        <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i>
                                        Cambiar a modo {{ $activeMode === 'landing' ? 'multipágina' : 'landing' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="tema-card__select-controls">
                            @if (count($theme['supports']) > 1)
                                <form method="POST" action="{{ route('dashboard.temas.activar', $slug) }}" class="tema-activar-form">
                                    @csrf
                                    <div class="tema-mode-selector">
                                        <label class="tema-mode-option">
                                            <input type="radio" name="mode" value="landing" checked>
                                            <span>
                                                <i class="fa-solid fa-scroll" aria-hidden="true"></i>
                                                Landing
                                            </span>
                                        </label>
                                        <label class="tema-mode-option">
                                            <input type="radio" name="mode" value="multipage">
                                            <span>
                                                <i class="fa-solid fa-window-restore" aria-hidden="true"></i>
                                                Multipágina
                                            </span>
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn--primary btn--full">
                                        <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
                                        Activar este tema
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('dashboard.temas.activar', $slug) }}" class="tema-activar-form">
                                    @csrf
                                    <input type="hidden" name="mode" value="{{ $theme['supports'][0] }}">
                                    <button type="submit" class="btn btn--primary btn--full">
                                        <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
                                        Activar este tema
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <div class="temas-custom panel">
        <div class="temas-custom__content">
            <i class="fa-solid fa-star temas-custom__icon" aria-hidden="true"></i>
            <div>
                <h3 class="temas-custom__title">¿Necesitas algo único?</h3>
                <p class="temas-custom__desc">Si ninguna plantilla se adapta perfectamente a tu imagen, puedo crear un diseño completamente personalizado para ti.</p>
            </div>
            <a href="https://victorroblesweb.es/contacto" target="_blank" rel="noopener" class="btn btn--primary">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                Solicitar diseño personalizado
            </a>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/temas.js') }}" defer></script>
@endpush
