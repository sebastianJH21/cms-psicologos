@extends('dashboard.layout')

@section('titulo', 'Categorías del blog')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Categorías'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Categorías del blog</h1>
            <p class="page-header__subtitle">Organiza los artículos por temáticas.</p>
        </div>
        <a href="{{ route('dashboard.blog.categorias.create') }}" class="btn btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Nueva categoría
        </a>
    </header>

    <section class="panel">
        @if ($categorias->count() === 0)
            <div class="empty-state">
                <i class="fa-regular fa-folder-open empty-state__icon" aria-hidden="true"></i>
                <p class="empty-state__title">Aún no hay categorías</p>
                <p class="empty-state__hint">Crea categorías para clasificar tus artículos.</p>
                <a href="{{ route('dashboard.blog.categorias.create') }}" class="btn btn--primary">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    Crear la primera categoría
                </a>
            </div>
        @else
            <div class="data-table__wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Orden</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Artículos</th>
                            <th scope="col" class="data-table__actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->orden }}</td>
                                <td>
                                    <strong>{{ $categoria->nombre }}</strong>
                                    @if ($categoria->descripcion)
                                        <small class="data-table__sub">{{ $categoria->descripcion }}</small>
                                    @endif
                                </td>
                                <td><code>{{ $categoria->slug }}</code></td>
                                <td>{{ $categoria->articulos_count }}</td>
                                <td class="data-table__actions">
                                    <a href="{{ route('dashboard.blog.categorias.edit', $categoria) }}" class="btn btn--icon" aria-label="Editar">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.blog.categorias.destroy', $categoria) }}"
                                        data-confirm="¿Eliminar esta categoría? Los artículos quedarán sin categoría asignada."
                                        data-confirm-title="Eliminar categoría"
                                        data-confirm-label="Sí, eliminar"
                                        data-confirm-style="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn--icon btn--icon-danger" aria-label="Eliminar">
                                            <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav class="pagination" aria-label="Paginación">
                {{ $categorias->links() }}
            </nav>
        @endif
    </section>
@endsection
