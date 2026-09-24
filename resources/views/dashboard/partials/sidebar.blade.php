@php
    $isActive = function ($matches) {
        foreach ((array) $matches as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }
        return false;
    };
@endphp

<aside class="sidebar" id="sidebar" aria-label="Navegación principal">
    <div class="sidebar__brand">
        <div class="sidebar__logo">
            <i class="fa-solid fa-brain" aria-hidden="true"></i>
        </div>
        <div class="sidebar__brand-text">
            <span class="sidebar__brand-name">PsicoCMS</span>
            <span class="sidebar__brand-sub">Panel administrativo</span>
        </div>
        <button type="button" class="sidebar__close" data-sidebar-close aria-label="Cerrar menú">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="sidebar__nav" aria-label="Secciones del panel">
        <ul class="sidebar__list">
            @foreach ($sidebarMenu as $entry)
                @if ($entry['type'] === 'item')
                    <li class="sidebar__item">
                        <a
                            href="{{ $entry['route'] ? route($entry['route']) : '#' }}"
                            @class([
                                'sidebar__link',
                                'sidebar__link--active' => $isActive($entry['match']),
                                'sidebar__link--disabled' => !$entry['route'],
                            ])
                            @if (!$entry['route']) aria-disabled="true" title="Disponible en una próxima fase" @endif
                        >
                            <i class="fa-solid {{ $entry['icon'] }} sidebar__icon" aria-hidden="true"></i>
                            <span>{{ $entry['label'] }}</span>
                        </a>
                    </li>
                @else
                    @php
                        $groupActive = false;
                        foreach ($entry['children'] as $child) {
                            if ($isActive($child['match'])) {
                                $groupActive = true;
                                break;
                            }
                        }
                    @endphp
                    <li @class(['sidebar__item', 'sidebar__item--group', 'sidebar__item--open' => $groupActive])>
                        <button type="button" class="sidebar__link sidebar__link--toggle" data-sidebar-toggle aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                            <i class="fa-solid {{ $entry['icon'] }} sidebar__icon" aria-hidden="true"></i>
                            <span>{{ $entry['label'] }}</span>
                            <i class="fa-solid fa-chevron-down sidebar__chevron" aria-hidden="true"></i>
                        </button>
                        <ul class="sidebar__sublist">
                            @foreach ($entry['children'] as $child)
                                <li class="sidebar__subitem">
                                    <a
                                        href="{{ $child['route'] ? route($child['route']) : '#' }}"
                                        @class([
                                            'sidebar__sublink',
                                            'sidebar__sublink--active' => $isActive($child['match']),
                                            'sidebar__sublink--disabled' => !$child['route'],
                                        ])
                                        @if (!$child['route']) aria-disabled="true" title="Disponible en una próxima fase" @endif
                                    >
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    <div class="sidebar__footer">
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="sidebar__ver-web" aria-label="Ver tu web pública">
            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            <span>Ver tu web</span>
        </a>

        <div class="sidebar__footer-bottom">
            <button
                type="button"
                class="sidebar__theme-toggle"
                id="btn-theme-toggle"
                aria-label="Cambiar apariencia del panel"
                title="Cambiar apariencia"
            >
                <i class="fa-solid fa-palette" aria-hidden="true"></i>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="sidebar__logout">
                @csrf
                <button type="submit" class="sidebar__logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="sidebar__backdrop" data-sidebar-backdrop hidden></div>
