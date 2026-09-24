@extends('dashboard.layout')

@section('titulo', 'Editar categoría')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Categorías', 'url' => route('dashboard.blog.categorias.index')],
        ['label' => $categoria->nombre],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Editar categoría</h1>
            <p class="page-header__subtitle">Modifica los datos de "{{ $categoria->nombre }}".</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.blog.categorias.update', $categoria) }}" class="cita-form" novalidate>
            @csrf
            @method('PUT')

            @include('dashboard.blog.categorias.partials.form')

            <div class="form-actions">
                <a href="{{ route('dashboard.blog.categorias.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>
@endsection
