@extends('dashboard.layout')

@section('titulo', 'Nueva categoría')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Categorías', 'url' => route('dashboard.blog.categorias.index')],
        ['label' => 'Nueva'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Nueva categoría</h1>
            <p class="page-header__subtitle">Crea una nueva temática para tus artículos.</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.blog.categorias.store') }}" class="cita-form" novalidate>
            @csrf

            @include('dashboard.blog.categorias.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.blog.categorias.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Crear categoría
                </button>
            </div>
        </form>
    </section>
@endsection
