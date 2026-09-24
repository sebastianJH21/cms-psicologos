@extends('dashboard.layout')

@section('titulo', 'Nuevo artículo')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Artículos', 'url' => route('dashboard.blog.articulos.index')],
        ['label' => 'Nuevo'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Nuevo artículo</h1>
            <p class="page-header__subtitle">Crea un nuevo contenido para tu blog.</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.blog.articulos.store') }}" class="cita-form" enctype="multipart/form-data" novalidate>
            @csrf

            @include('dashboard.blog.articulos.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.blog.articulos.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar artículo
                </button>
            </div>
        </form>
    </section>
@endsection
