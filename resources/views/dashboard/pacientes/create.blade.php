@extends('dashboard.layout')

@section('titulo', 'Nuevo paciente')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => 'Nuevo paciente'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/pacientes.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Nuevo paciente</h1>
            <p class="page-header__subtitle">Crea manualmente la ficha de un paciente.</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.pacientes.store') }}" class="cita-form">
            @csrf
            @include('dashboard.pacientes.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.pacientes.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Crear paciente
                </button>
            </div>
        </form>
    </section>
@endsection
