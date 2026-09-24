@extends('dashboard.layout')

@section('titulo', 'Especialidades')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Especialidades'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-brain" aria-hidden="true"></i> Especialidades</h1>
            <p class="page-header__subtitle">Áreas y especialidades en las que centras tu trabajo.</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.configuracion.terapias.create') }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nueva especialidad
            </a>
        </div>
    </header>

    @if ($terapias->isEmpty())
        <div class="empty-state panel">
            <i class="fa-solid fa-brain empty-state__icon" aria-hidden="true"></i>
            <p class="empty-state__title">Sin especialidades</p>
            <p class="empty-state__hint">Añade las especialidades que tratas en tu consulta.</p>
            <a href="{{ route('dashboard.configuracion.terapias.create') }}" class="btn btn--primary" style="margin-top:1.2rem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Crear primera especialidad
            </a>
        </div>
    @else
        <div class="config-grid">
            @foreach ($terapias as $terapia)
                <article class="config-card panel">
                    <div class="config-card__icon">
                        <i class="fa-solid {{ $terapia->icono ?: 'fa-brain' }}" aria-hidden="true"></i>
                    </div>
                    <div class="config-card__body">
                        <h3 class="config-card__titulo">
                            {{ $terapia->titulo }}
                            @if (!$terapia->activo)
                                <span class="badge badge--warning">Inactiva</span>
                            @endif
                        </h3>
                        @if ($terapia->descripcion)
                            <p class="config-card__desc">{{ Str::limit($terapia->descripcion, 140) }}</p>
                        @endif
                    </div>
                    <div class="config-card__actions">
                        <a href="{{ route('dashboard.configuracion.terapias.edit', $terapia) }}"
                            class="btn btn--icon" aria-label="Editar">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                        </a>
                        <form method="POST" action="{{ route('dashboard.configuracion.terapias.destroy', $terapia) }}"
                            data-confirm="¿Eliminar esta especialidad?"
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
