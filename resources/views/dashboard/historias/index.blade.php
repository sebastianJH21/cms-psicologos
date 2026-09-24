@extends('dashboard.layout')

@section('titulo', 'Historias clínicas')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Historias clínicas'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/historias.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pagination.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-folder-open" aria-hidden="true"></i> Historias clínicas</h1>
            <p class="page-header__subtitle">Registro de todas las sesiones de terapia</p>
        </div>
    </header>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('dashboard.historias.index') }}"
        id="form-buscar-historias" class="historias-filtros panel">
        <div class="form-field" style="flex:1;margin:0">
            <label for="filtro-q" class="sr-only">Buscar paciente</label>
            <input type="text" id="filtro-q" name="q" placeholder="Buscar por nombre de paciente..."
                value="{{ request('q') }}" autocomplete="off">
        </div>
        <button type="submit" class="btn btn--primary">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            Buscar
        </button>
        <button type="button" id="btn-limpiar-historias" class="btn btn--ghost">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            Limpiar
        </button>
    </form>

    <div id="tabla-historias-wrapper" class="historias-tabla-wrapper">
        @include('dashboard.historias.partials.tabla')
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/historias.js') }}" defer></script>
@endpush
