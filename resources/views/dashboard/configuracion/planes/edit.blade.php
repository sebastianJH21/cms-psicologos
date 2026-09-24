@extends('dashboard.layout')

@section('titulo', 'Editar plan')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Planes y precios', 'url' => route('dashboard.configuracion.planes.index')],
        ['label' => 'Editar'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <h1 class="page-header__title">Editar plan</h1>
        <a href="{{ route('dashboard.configuracion.planes.index') }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Cancelar
        </a>
    </header>

    <div class="panel">
        <form method="POST" action="{{ route('dashboard.configuracion.planes.update', $plan) }}" class="cita-form">
            @csrf @method('PUT')
            @include('dashboard.configuracion.planes.partials.form')
            <div class="form-actions">
                <a href="{{ route('dashboard.configuracion.planes.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
