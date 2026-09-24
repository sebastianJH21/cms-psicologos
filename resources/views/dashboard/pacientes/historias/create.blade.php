@extends('dashboard.layout')

@section('titulo', 'Nueva entrada — ' . $paciente->nombre_completo)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo, 'url' => route('dashboard.pacientes.show', $paciente)],
        ['label' => 'Historia clínica', 'url' => route('dashboard.pacientes.historias.index', $paciente)],
        ['label' => 'Nueva entrada'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/historias.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-file-medical" aria-hidden="true"></i> Nueva entrada de historia</h1>
            <p class="page-header__subtitle">{{ $paciente->nombre_completo }}</p>
        </div>
        <a href="{{ route('dashboard.pacientes.historias.index', $paciente) }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Cancelar
        </a>
    </header>

    <div class="panel">
        <form method="POST"
            action="{{ route('dashboard.pacientes.historias.store', $paciente) }}"
            enctype="multipart/form-data"
            class="cita-form">
            @csrf

            @include('dashboard.pacientes.historias.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.pacientes.historias.index', $paciente) }}" class="btn btn--ghost">
                    Cancelar
                </a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar entrada
                </button>
            </div>
        </form>
    </div>
@endsection
