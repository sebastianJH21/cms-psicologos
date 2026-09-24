@extends('dashboard.layout')

@section('titulo', 'Editar pregunta')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Preguntas frecuentes', 'url' => route('dashboard.faqs.index')],
        ['label' => 'Editar'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/faqs.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Editar pregunta</h1>
        </div>
        <a href="{{ route('dashboard.faqs.index') }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Cancelar
        </a>
    </header>

    <div class="panel">
        <form method="POST" action="{{ route('dashboard.faqs.update', $faq) }}" class="cita-form">
            @csrf
            @method('PUT')
            @include('dashboard.faqs.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.faqs.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
