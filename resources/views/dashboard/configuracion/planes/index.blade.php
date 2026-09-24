@extends('dashboard.layout')

@section('titulo', 'Planes y precios')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Planes y precios'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-tags" aria-hidden="true"></i> Planes y precios</h1>
            <p class="page-header__subtitle">Modalidades y tarifas de tus sesiones, online y presenciales.</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.configuracion.planes.create') }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nuevo plan
            </a>
        </div>
    </header>

    <section class="panel">
        <header class="panel__header">
            <h2 class="panel__title">
                <i class="fa-solid fa-video" aria-hidden="true"></i>
                Sesiones online
            </h2>
        </header>
        @if ($planesOnline->isEmpty())
            <div class="empty-state empty-state--inline">
                <p class="empty-state__hint">Sin planes online configurados.</p>
            </div>
        @else
            @include('dashboard.configuracion.planes.partials.lista', ['planes' => $planesOnline])
        @endif
    </section>

    <section class="panel">
        <header class="panel__header">
            <h2 class="panel__title">
                <i class="fa-solid fa-house-medical" aria-hidden="true"></i>
                Sesiones presenciales
            </h2>
        </header>
        @if ($planesPresencial->isEmpty())
            <div class="empty-state empty-state--inline">
                <p class="empty-state__hint">Sin planes presenciales configurados.</p>
            </div>
        @else
            @include('dashboard.configuracion.planes.partials.lista', ['planes' => $planesPresencial])
        @endif
    </section>
@endsection
