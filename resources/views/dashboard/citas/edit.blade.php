@extends('dashboard.layout')

@section('titulo', 'Editar cita')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Citas', 'url' => route('dashboard.citas.index')],
        ['label' => 'Editar #' . $cita->id],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Editar cita #{{ $cita->id }}</h1>
            <p class="page-header__subtitle">Actualiza los datos de la sesión.</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.citas.update', $cita) }}" class="cita-form">
            @csrf
            @method('PUT')
            @include('dashboard.citas.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.citas.show', $cita) }}" class="btn btn--ghost">Volver</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/cita-form.js') }}" defer></script>
@endpush
