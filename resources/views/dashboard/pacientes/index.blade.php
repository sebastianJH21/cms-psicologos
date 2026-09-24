@extends('dashboard.layout')

@section('titulo', 'Pacientes')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/pacientes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pagination.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-users" aria-hidden="true"></i> Pacientes</h1>
            <p class="page-header__subtitle">Gestiona la ficha de cada persona que acude a consulta.</p>
        </div>
        <div class="page-header__actions">
            @if ($papelera)
                <a href="{{ route('dashboard.pacientes.index') }}" class="btn btn--ghost">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Volver al listado
                </a>
            @else
                <a href="{{ route('dashboard.pacientes.index', ['papelera' => 1]) }}" class="btn btn--ghost">
                    <i class="fa-solid fa-trash-can-arrow-up" aria-hidden="true"></i>
                    Papelera
                </a>
                <a href="{{ route('dashboard.pacientes.create') }}" class="btn btn--primary">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    Nuevo paciente
                </a>
            @endif
        </div>
    </header>

    <section class="panel">
        <form method="GET" action="{{ route('dashboard.pacientes.index') }}" class="pacientes-filtros" id="form-filtros-pacientes">
            <div class="form-field">
                <label for="filtro-q">Buscar</label>
                <input type="search" id="filtro-q" name="q" value="{{ $filtros['q'] }}" placeholder="Nombre, apellidos, teléfono o email">
            </div>
            <div class="form-field">
                <label for="filtro-origen">Origen</label>
                <select id="filtro-origen" name="origen">
                    <option value="">Todos</option>
                    <option value="manual" {{ $filtros['origen'] === 'manual' ? 'selected' : '' }}>Manual</option>
                    <option value="publica" {{ $filtros['origen'] === 'publica' ? 'selected' : '' }}>Reserva pública</option>
                </select>
            </div>
            @if ($papelera)
                <input type="hidden" name="papelera" value="1">
            @endif
            <div class="pacientes-filtros__actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-filter" aria-hidden="true"></i>
                    Filtrar
                </button>
                <button type="button" class="btn btn--ghost" id="btn-limpiar-filtros">Limpiar</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div id="tabla-pacientes-wrapper">
            @include('dashboard.pacientes.partials.tabla', compact('pacientes', 'papelera'))
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/pacientes.js') }}"></script>
@endpush
