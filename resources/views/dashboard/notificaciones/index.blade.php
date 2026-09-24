@extends('dashboard.layout')

@section('titulo', 'Notificaciones')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Notificaciones'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/notificaciones.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-regular fa-bell" aria-hidden="true"></i>
                Notificaciones
            </h1>
            <p class="page-header__subtitle">Aquí verás las nuevas reservas de citas que tus pacientes hagan desde tu web pública.</p>
        </div>
    </header>

    <section class="panel">
        <h2 class="panel__title">
            <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
            Nuevas reservas ({{ $nuevas->count() }})
        </h2>

        @if ($nuevas->isEmpty())
            <div class="empty-state">
                <i class="fa-regular fa-bell-slash empty-state__icon" aria-hidden="true"></i>
                <p class="empty-state__title">No tienes notificaciones nuevas</p>
                <p class="empty-state__hint">Cuando un paciente reserve una cita desde tu web, aparecerá aquí.</p>
            </div>
        @else
            <ul class="notif-list">
                @foreach ($nuevas as $cita)
                    <li class="notif-list__item notif-list__item--new">
                        <div class="notif-list__icon">
                            <i class="fa-solid fa-calendar-plus" aria-hidden="true"></i>
                        </div>
                        <div class="notif-list__body">
                            <strong class="notif-list__title">
                                {{ $cita->nombre_provisional ?? ($cita->paciente?->nombre_completo ?? 'Sin nombre') }}
                                ha reservado una cita
                            </strong>
                            <span class="notif-list__meta">
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                {{ $cita->fecha_inicio->format('d/m/Y H:i') }}
                                ·
                                <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">{{ ucfirst($cita->modalidad) }}</span>
                            </span>
                            <span class="notif-list__time">{{ $cita->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('dashboard.citas.show', $cita) }}" class="btn btn--icon" aria-label="Ver detalle">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($vistas->isNotEmpty())
        <section class="panel">
            <h2 class="panel__title">
                <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                Anteriores
            </h2>
            <ul class="notif-list">
                @foreach ($vistas as $cita)
                    <li class="notif-list__item">
                        <div class="notif-list__icon notif-list__icon--muted">
                            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                        </div>
                        <div class="notif-list__body">
                            <strong class="notif-list__title">
                                {{ $cita->nombre_provisional ?? ($cita->paciente?->nombre_completo ?? 'Sin nombre') }}
                            </strong>
                            <span class="notif-list__meta">
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                {{ $cita->fecha_inicio->format('d/m/Y H:i') }}
                                ·
                                <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">{{ ucfirst($cita->modalidad) }}</span>
                            </span>
                            <span class="notif-list__time">{{ $cita->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('dashboard.citas.show', $cita) }}" class="btn btn--icon" aria-label="Ver detalle">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
