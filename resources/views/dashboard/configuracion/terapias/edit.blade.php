@extends('dashboard.layout')

@section('titulo', 'Editar especialidad')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Especialidades', 'url' => route('dashboard.configuracion.terapias.index')],
        ['label' => 'Editar'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <h1 class="page-header__title"><i class="fa-solid fa-brain" aria-hidden="true"></i> Editar especialidad</h1>
        <a href="{{ route('dashboard.configuracion.terapias.index') }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Cancelar
        </a>
    </header>

    <div class="panel">
        <form method="POST" action="{{ route('dashboard.configuracion.terapias.update', $terapia) }}" class="cita-form">
            @csrf @method('PUT')
            @include('dashboard.configuracion.terapias.partials.form')
            <div class="form-actions">
                <a href="{{ route('dashboard.configuracion.terapias.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    @include('dashboard.configuracion.partials.icon-picker')
@endsection
