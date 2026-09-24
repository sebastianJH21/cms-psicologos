@extends('dashboard.layout')

@section('titulo', 'Editar artículo')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Artículos', 'url' => route('dashboard.blog.articulos.index')],
        ['label' => $articulo->titulo],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Editar artículo</h1>
            <p class="page-header__subtitle">Modifica el contenido y los metadatos.</p>
        </div>
        <a href="{{ route('dashboard.blog.articulos.show', $articulo) }}" class="btn btn--ghost">
            <i class="fa-solid fa-eye" aria-hidden="true"></i>
            Vista previa
        </a>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.blog.articulos.update', $articulo) }}" class="cita-form" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            @include('dashboard.blog.articulos.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.blog.articulos.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>
@endsection
