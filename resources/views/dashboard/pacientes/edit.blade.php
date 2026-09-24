@extends('dashboard.layout')

@section('titulo', 'Editar paciente')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo, 'url' => route('dashboard.pacientes.show', $paciente)],
        ['label' => 'Editar'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/pacientes.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Editar paciente</h1>
            <p class="page-header__subtitle">{{ $paciente->nombre_completo }}</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.pacientes.update', $paciente) }}" class="cita-form">
            @csrf
            @method('PUT')
            @include('dashboard.pacientes.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.pacientes.show', $paciente) }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>
@endsection
