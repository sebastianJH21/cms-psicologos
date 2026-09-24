@extends('dashboard.layout')

@section('titulo', 'Historia de ' . $paciente->nombre_completo)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo, 'url' => route('dashboard.pacientes.show', $paciente)],
        ['label' => 'Historia clínica'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/historias.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pagination.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Historia clínica</h1>
            <p class="page-header__subtitle">{{ $paciente->nombre_completo }} &middot; {{ $paciente->telefono }}</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.pacientes.show', $paciente) }}" class="btn btn--ghost">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Volver al paciente
            </a>
            <a href="{{ route('dashboard.pacientes.historias.create', $paciente) }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nueva entrada
            </a>
        </div>
    </header>

    @if ($historias->isEmpty())
        <div class="empty-state panel">
            <i class="fa-solid fa-notes-medical empty-state__icon" aria-hidden="true"></i>
            <p class="empty-state__title">Sin entradas de historia</p>
            <p class="empty-state__hint">Registra aquí las notas de cada sesión de terapia con este paciente.</p>
            <a href="{{ route('dashboard.pacientes.historias.create', $paciente) }}" class="btn btn--primary" style="margin-top:1.2rem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Crear primera entrada
            </a>
        </div>
    @else
        <div class="historia-timeline">
            @foreach ($historias as $historia)
                <article class="historia-item panel">
                    <div class="historia-item__aside">
                        <div class="historia-item__fecha">
                            <span class="historia-item__dia">{{ $historia->fecha_sesion->format('d') }}</span>
                            <span class="historia-item__mes">{{ strtoupper($historia->fecha_sesion->translatedFormat('M Y')) }}</span>
                        </div>
                        @if ($historia->archivos_count > 0)
                            <span class="historia-item__archivos-badge">
                                <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                                {{ $historia->archivos_count }}
                            </span>
                        @endif
                    </div>
                    <div class="historia-item__body">
                        <h2 class="historia-item__titulo">{{ $historia->titulo_mostrado }}</h2>
                        <div class="historia-item__excerpt">
                            {!! Str::limit(strip_tags($historia->contenido), 200) !!}
                        </div>
                    </div>
                    <div class="historia-item__actions">
                        <a href="{{ route('dashboard.pacientes.historias.show', [$paciente, $historia]) }}"
                            class="btn btn--ghost btn--sm">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            Ver
                        </a>
                        <a href="{{ route('dashboard.pacientes.historias.edit', [$paciente, $historia]) }}"
                            class="btn btn--ghost btn--sm">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            Editar
                        </a>
                        <form method="POST"
                            action="{{ route('dashboard.pacientes.historias.destroy', [$paciente, $historia]) }}"
                            data-confirm="¿Eliminar esta entrada de historia? Esta acción no se puede deshacer."
                            class="historia-item__delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--icon btn--icon-danger btn--sm" aria-label="Eliminar">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($historias->hasPages())
            <div class="pagination">
                {{ $historias->links() }}
            </div>
        @endif
    @endif
@endsection
