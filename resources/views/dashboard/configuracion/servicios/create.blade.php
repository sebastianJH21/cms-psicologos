@extends('dashboard.layout')

@section('titulo', 'Nuevo servicio')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Servicios', 'url' => route('dashboard.configuracion.servicios.index')],
        ['label' => 'Nuevo'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <h1 class="page-header__title"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> Nuevo servicio</h1>
        <a href="{{ route('dashboard.configuracion.servicios.index') }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Cancelar
        </a>
    </header>

    <div class="panel">
        <form method="POST" action="{{ route('dashboard.configuracion.servicios.store') }}" class="cita-form">
            @csrf
            @include('dashboard.configuracion.servicios.partials.form')
            <div class="form-actions">
                <a href="{{ route('dashboard.configuracion.servicios.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Crear servicio
                </button>
            </div>
        </form>
    </div>

    @include('dashboard.configuracion.partials.icon-picker')
@endsection
