@php
    use Illuminate\Support\Facades\Storage;
    $usuario = auth()->user();
    $iniciales = strtoupper(mb_substr($usuario->nombre ?? '?', 0, 1) . mb_substr($usuario->apellidos ?? '', 0, 1));
    $avatarUrl = $usuario->avatar_path ? route('dashboard.perfil-privado.avatar') : null;
@endphp

<header class="topbar">
    <button type="button" class="topbar__menu" data-sidebar-open aria-label="Abrir menú lateral">
        <i class="fa-solid fa-bars" aria-hidden="true"></i>
    </button>

    <form class="topbar__search" method="GET" action="{{ route('dashboard.buscador.index') }}" role="search">
        <label class="topbar__search-label" for="topbar-search">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input
                type="search"
                id="topbar-search"
                name="q"
                class="topbar__search-input"
                placeholder="Buscar pacientes, citas, historias, artículos..."
                autocomplete="off"
                value="{{ request()->routeIs('dashboard.buscador.*') ? request('q') : '' }}"
            >
        </label>
    </form>

    <div class="topbar__actions">
        <a href="{{ route('dashboard.notificaciones.index') }}" class="topbar__action topbar__action--link" aria-label="Notificaciones" title="Notificaciones">
            <i class="fa-regular fa-bell" aria-hidden="true"></i>
            @if (($nuevasReservasCount ?? 0) > 0)
                <span class="topbar__badge" aria-label="{{ $nuevasReservasCount }} nuevas">
                    {{ $nuevasReservasCount > 99 ? '99+' : $nuevasReservasCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('dashboard.ayuda.index') }}" class="topbar__action topbar__action--link" aria-label="Ayuda" title="Ayuda y tutorial">
            <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        </a>

        <a href="{{ route('dashboard.perfil-privado.edit') }}" class="topbar__user" aria-label="Tu perfil y configuración">
            <span class="topbar__user-name">Psc. {{ $usuario->nombre }}</span>
            <span class="topbar__user-avatar" aria-hidden="true">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $usuario->nombre_completo }}" class="topbar__user-avatar-img">
                @else
                    {{ $iniciales }}
                @endif
            </span>
        </a>
    </div>
</header>
