@extends('dashboard.layout')

@section('titulo', 'Citas de ' . $paciente->nombre_completo)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo, 'url' => route('dashboard.pacientes.show', $paciente)],
        ['label' => 'Citas'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/pacientes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pagination.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Historial de citas</h1>
            <p class="page-header__subtitle">{{ $paciente->nombre_completo }}</p>
        </div>
        <a href="{{ route('dashboard.pacientes.show', $paciente) }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Volver a la ficha
        </a>
    </header>

    <section class="panel">
        @if ($citas->count() === 0)
            <div class="empty-state">
                <i class="fa-regular fa-calendar empty-state__icon" aria-hidden="true"></i>
                <p class="empty-state__title">Este paciente no tiene citas</p>
                <p class="empty-state__hint">Agenda una nueva desde la sección de citas.</p>
            </div>
        @else
            <div class="data-table__wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Modalidad</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Origen</th>
                            <th scope="col" class="data-table__actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($citas as $cita)
                            <tr @class(['data-table__row--muted' => $cita->trashed()])>
                                <td>
                                    <strong>{{ $cita->fecha_inicio->format('d/m/Y') }}</strong>
                                    <small class="data-table__sub">{{ $cita->fecha_inicio->format('H:i') }} – {{ $cita->fecha_fin->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">
                                        {{ $cita->modalidad_label }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $estadoClase = match ($cita->estado) {
                                            'confirmada' => 'success',
                                            'realizada' => 'info',
                                            'cancelada' => 'danger',
                                            'no_asistio' => 'warning',
                                            default => 'info',
                                        };
                                    @endphp
                                    <span class="badge badge--{{ $estadoClase }}">{{ $cita->estado_label }}</span>
                                </td>
                                <td>{{ $cita->origen === 'publica' ? 'Pública' : 'Manual' }}</td>
                                <td class="data-table__actions">
                                    @if($cita->trashed())
                                        <span class="btn btn--icon btn--disabled" aria-label="Ver">
                                            <i class="fa-regular fa-circle-xmark"></i>
                                        </span>
                                    @else
                                        <a href="{{ route('dashboard.citas.show', $cita) }}" class="btn btn--icon" aria-label="Ver">
                                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav class="pagination" aria-label="Paginación">
                {{ $citas->links() }}
            </nav>
        @endif
    </section>
@endsection
