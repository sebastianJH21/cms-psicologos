@extends('dashboard.layout')

@section('titulo', 'Nueva cita')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Citas', 'url' => route('dashboard.citas.index')],
        ['label' => 'Nueva cita'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pacientes.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Nueva cita</h1>
            <p class="page-header__subtitle">Agenda manualmente una sesión.</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.citas.store') }}" class="cita-form">
            @csrf
            @include('dashboard.citas.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.citas.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Crear cita
                </button>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/cita-form.js') }}"></script>
@endpush
