@extends('dashboard.layout')

@section('titulo', 'Servicios')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Servicios'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> Servicios</h1>
            <p class="page-header__subtitle">Servicios principales que se mostrarán en la web pública.</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.configuracion.servicios.create') }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nuevo servicio
            </a>
        </div>
    </header>

    @if ($servicios->isEmpty())
        <div class="empty-state panel">
            <i class="fa-solid fa-briefcase empty-state__icon" aria-hidden="true"></i>
            <p class="empty-state__title">Sin servicios</p>
            <p class="empty-state__hint">Crea los servicios principales que ofreces.</p>
            <a href="{{ route('dashboard.configuracion.servicios.create') }}" class="btn btn--primary" style="margin-top:1.2rem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Crear primer servicio
            </a>
        </div>
    @else
        <div class="config-grid">
            @foreach ($servicios as $servicio)
                <article class="config-card panel">
                    <div class="config-card__icon">
                        <i class="{{ $servicio->icono ?: 'fa-solid fa-briefcase' }}" aria-hidden="true"></i>
                    </div>
                    <div class="config-card__body">
                        <h3 class="config-card__titulo">
                            {{ $servicio->titulo }}
                            @if (!$servicio->activo)
                                <span class="badge badge--warning">Inactivo</span>
                            @endif
                        </h3>
                        @if ($servicio->descripcion)
                            <p class="config-card__desc">{{ Str::limit($servicio->descripcion, 140) }}</p>
                        @endif
                    </div>
                    <div class="config-card__actions">
                        <a href="{{ route('dashboard.configuracion.servicios.edit', $servicio) }}"
                            class="btn btn--icon" aria-label="Editar">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                        </a>
                        <form method="POST" action="{{ route('dashboard.configuracion.servicios.destroy', $servicio) }}"
                            data-confirm="¿Eliminar este servicio?"
                            data-ajax-delete="true"
                            data-delete-target="article"
                            style="display:contents">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn--icon btn--icon-danger" aria-label="Eliminar">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
