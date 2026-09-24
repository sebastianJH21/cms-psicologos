@extends('dashboard.layout')

@section('titulo', 'Editar — ' . $historia->titulo_mostrado)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo, 'url' => route('dashboard.pacientes.show', $paciente)],
        ['label' => 'Historia clínica', 'url' => route('dashboard.pacientes.historias.index', $paciente)],
        ['label' => $historia->titulo_mostrado, 'url' => route('dashboard.pacientes.historias.show', [$paciente, $historia])],
        ['label' => 'Editar'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/historias.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Editar entrada</h1>
            <p class="page-header__subtitle">{{ $historia->titulo_mostrado }} &middot; {{ $paciente->nombre_completo }}</p>
        </div>
        <a href="{{ route('dashboard.pacientes.historias.show', [$paciente, $historia]) }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Cancelar
        </a>
    </header>

    {{-- ── Formulario de edición — sin forms anidados ──────────────────────── --}}
    <div class="panel">
        <form method="POST"
            action="{{ route('dashboard.pacientes.historias.update', [$paciente, $historia]) }}"
            enctype="multipart/form-data"
            class="cita-form">
            @csrf
            @method('PUT')

            @include('dashboard.pacientes.historias.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.pacientes.historias.show', [$paciente, $historia]) }}" class="btn btn--ghost">
                    Cancelar
                </a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- ── Archivos adjuntos actuales — FUERA del form de edición ─────────── --}}
    @if ($historia->archivos->isNotEmpty())
        <div class="panel">
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                    Archivos adjuntos actuales
                </h2>
            </header>
            <div class="historia-archivos-edit">
                @foreach ($historia->archivos as $archivo)
                    <div class="historia-archivo-edit">
                        @if ($archivo->tipo === 'imagen')
                            <img src="{{ $archivo->url }}" alt="{{ $archivo->nombre_original }}"
                                class="historia-archivo-edit__thumb">
                        @else
                            <div class="historia-archivo-edit__pdf">
                                <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                            </div>
                        @endif
                        <span class="historia-archivo-edit__nombre" title="{{ $archivo->nombre_original }}">
                            {{ Str::limit($archivo->nombre_original, 24) }}
                        </span>
                        <span class="historia-archivo-edit__tamanio">{{ $archivo->tamanio_formateado }}</span>
                        <form method="POST"
                            action="{{ route('dashboard.pacientes.historias.archivos.destroy', [$paciente, $historia, $archivo]) }}"
                            data-confirm="¿Eliminar este archivo? Esta acción no se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--icon btn--icon-danger btn--sm" aria-label="Eliminar archivo">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
