@extends('dashboard.layout')

@section('titulo', 'Nueva pregunta')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Preguntas frecuentes', 'url' => route('dashboard.faqs.index')],
        ['label' => 'Nueva'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/faqs.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-circle-question" aria-hidden="true"></i> Nueva pregunta</h1>
        </div>
        <a href="{{ route('dashboard.faqs.index') }}" class="btn btn--ghost">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Cancelar
        </a>
    </header>

    <div class="panel">
        <form method="POST" action="{{ route('dashboard.faqs.store') }}" class="cita-form">
            @csrf
            @include('dashboard.faqs.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.faqs.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Crear pregunta
                </button>
            </div>
        </form>
    </div>
@endsection
