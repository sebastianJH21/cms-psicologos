@extends('dashboard.layout')

@section('titulo', 'Protección de datos')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Configuración'],
        ['label' => 'Protección de datos'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Protección de datos</h1>
            <p class="page-header__subtitle">
                Configura la plantilla del documento de protección de datos que se entregará a tus pacientes.
            </p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.configuracion.proteccion-datos.descargar-vacio') }}"
                class="btn btn--ghost" target="_blank" rel="noopener">
                <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
                Descargar plantilla vacía (PDF)
            </a>
        </div>
    </header>

    <section class="panel">
        <header class="panel__header">
            <h2 class="panel__title">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                Marcadores disponibles
            </h2>
        </header>
        <p class="panel__subtitle">
            Inserta estos marcadores en la plantilla. Cuando descargues el PDF de un paciente concreto,
            se sustituirán automáticamente por sus datos reales.
        </p>
        <div class="placeholders-grid">
            @foreach ($placeholders as $tag => $descripcion)
                <div class="placeholder-item">
                    <code class="placeholder-item__code">{{ $tag }}</code>
                    <span class="placeholder-item__desc">{{ $descripcion }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <form method="POST"
            action="{{ route('dashboard.configuracion.proteccion-datos.update') }}"
            class="cita-form">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="plantilla_html">
                    <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                    Contenido de la plantilla
                </label>
                <textarea id="plantilla_html" name="plantilla_html" data-jodit data-jodit-no-image rows="25">{{ old('plantilla_html', $plantilla) }}</textarea>
                @error('plantilla_html')<small class="form-field__error">{{ $message }}</small>@enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar plantilla
                </button>
            </div>
        </form>
    </section>

    @include('dashboard.blog.partials.editor-jodit')
@endsection
