@extends('dashboard.layout')

@section('titulo', 'Artículos del blog')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Artículos'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/pagination.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Artículos del blog</h1>
            <p class="page-header__subtitle">Crea, edita y publica los contenidos de tu web.</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.blog.categorias.index') }}" class="btn btn--ghost">
                <i class="fa-solid fa-folder-tree" aria-hidden="true"></i>
                Categorías
            </a>
            <a href="{{ route('dashboard.blog.articulos.create') }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nuevo artículo
            </a>
        </div>
    </header>

    <section class="panel">
        <button type="button" class="filtros-toggle" id="toggle-filtros-blog" aria-expanded="false" aria-controls="form-filtros-blog">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <span>Buscar y filtrar</span>
        </button>
        <form id="form-filtros-blog" method="GET" action="{{ route('dashboard.blog.articulos.index') }}" class="blog-filtros">
            <div class="form-field">
                <label for="filtro-q">Buscar</label>
                <input type="search" id="filtro-q" name="q" value="{{ $filtros['q'] }}" placeholder="Título o extracto">
            </div>
            <div class="form-field">
                <label for="filtro-estado">Estado</label>
                <select id="filtro-estado" name="estado">
                    <option value="">Todos</option>
                    @foreach ($estados as $k => $l)
                        <option value="{{ $k }}" {{ $filtros['estado'] === $k ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="filtro-categoria">Categoría</label>
                <select id="filtro-categoria" name="categoria_id">
                    <option value="">Todas</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $filtros['categoria_id'] === (string) $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="blog-filtros__actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-filter" aria-hidden="true"></i>
                    Filtrar
                </button>
                <button type="button" id="btn-limpiar-blog" class="btn btn--ghost">Limpiar</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div id="tabla-articulos-wrapper">
            @include('dashboard.blog.articulos.partials.tabla', compact('articulos'))
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/blog.js') }}" defer></script>
    <script>
        (function () {
            const toggle = document.getElementById('toggle-filtros-blog');
            const form = document.getElementById('form-filtros-blog');
            if (!toggle || !form) {
                return;
            }
            toggle.addEventListener('click', function () {
                const abierto = form.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });
        })();
    </script>
@endpush
