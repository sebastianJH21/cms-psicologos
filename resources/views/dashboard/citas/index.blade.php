@extends('dashboard.layout')

@section('titulo', 'Citas')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Citas'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pagination.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Gestión de citas</h1>
            <p class="page-header__subtitle">Listado completo con filtros y acciones rápidas.</p>
        </div>
        <a href="{{ route('dashboard.citas.create') }}" class="btn btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Nueva cita
        </a>
    </header>

    <section class="panel">
        <button type="button" class="filtros-toggle" id="toggle-filtros-citas" aria-expanded="false" aria-controls="form-filtros-citas">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <span>Buscar y filtrar</span>
        </button>
        <form method="GET" action="{{ route('dashboard.citas.index') }}" class="citas-filtros" id="form-filtros-citas">
            <input type="hidden" name="periodo" id="filtro-periodo" value="{{ $filtros['periodo'] ?? 'proximas' }}">
            <div class="citas-tabs">
                <button type="button" class="citas-tab {{ ($filtros['periodo'] ?? 'proximas') === 'proximas' ? 'is-active' : '' }}" data-periodo="proximas">
                    <i class="fa-solid fa-calendar-day" aria-hidden="true"></i> Próximas citas
                </button>
                <button type="button" class="citas-tab citas-tab--past {{ ($filtros['periodo'] ?? 'proximas') === 'pasadas' ? 'is-active' : '' }}" data-periodo="pasadas">
                    <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Citas pasadas
                </button>
            </div>
            <div class="form-field">
                <label for="filtro-q">Buscar</label>
                <input type="search" id="filtro-q" name="q" value="{{ $filtros['q'] }}" placeholder="Nombre o teléfono">
            </div>
            <div class="form-field">
                <label for="filtro-modalidad">Modalidad</label>
                <select id="filtro-modalidad" name="modalidad">
                    <option value="">Todas</option>
                    @foreach ($modalidades as $k => $l)
                        <option value="{{ $k }}" {{ $filtros['modalidad'] === $k ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="filtro-estado">Estado</label>
                <select id="filtro-estado" name="estado">
                    <option value="">Todos</option>
                    @foreach ($estados as $k => $l)
                        <option value="{{ $k }}" {{ $filtros['estado'] === $k ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="filtro-desde">Desde</label>
                <input type="date" id="filtro-desde" name="desde" value="{{ $filtros['desde'] }}">
            </div>
            <div class="form-field">
                <label for="filtro-hasta">Hasta</label>
                <input type="date" id="filtro-hasta" name="hasta" value="{{ $filtros['hasta'] }}">
            </div>
            <div class="citas-filtros__actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-filter" aria-hidden="true"></i>
                    Filtrar
                </button>
                <button type="button" class="btn btn--ghost" id="btn-limpiar-citas">Limpiar</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div id="tabla-citas-wrapper">
            @include('dashboard.citas.partials.tabla', compact('citas'))
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/citas.js') }}"></script>
    <script src="{{ asset('js/dashboard/cita-estado.js') }}" defer></script>
    <script>
        (function () {
            const toggle = document.getElementById('toggle-filtros-citas');
            const form = document.getElementById('form-filtros-citas');
            if (!toggle || !form) {
                return;
            }
            toggle.addEventListener('click', function () {
                const abierto = form.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });
        })();
    </script>
@endpush
