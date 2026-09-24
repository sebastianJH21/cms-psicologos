@extends('dashboard.layout')

@section('titulo', $articulo->titulo)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Blog'],
        ['label' => 'Artículos', 'url' => route('dashboard.blog.articulos.index')],
        ['label' => $articulo->titulo],
    ];

    $estadoClase = match ($articulo->estado) {
        'publicado' => 'success',
        'archivado' => 'warning',
        default => 'info',
    };
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/blog.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">{{ $articulo->titulo }}</h1>
            <p class="page-header__subtitle">
                <span class="badge badge--{{ $estadoClase }}">{{ $articulo->estado_label }}</span>
                @if ($articulo->categoria)
                    · <span class="badge badge--info">{{ $articulo->categoria->nombre }}</span>
                @endif
                @if ($articulo->published_at)
                    · {{ $articulo->published_at->format('d/m/Y H:i') }}
                @endif
            </p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.blog.articulos.index') }}" class="btn btn--ghost">Volver</a>
            <a href="{{ route('dashboard.blog.articulos.edit', $articulo) }}" class="btn btn--primary">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                Editar
            </a>
        </div>
    </header>

    <article class="panel blog-show">
        @if ($articulo->imagen_url)
            <img src="{{ $articulo->imagen_url }}" alt="{{ $articulo->titulo }}" class="blog-show__imagen">
        @endif

        @if ($articulo->extracto)
            <p class="blog-show__extracto">{{ $articulo->extracto }}</p>
        @endif

        <div class="blog-show__contenido wysiwyg">
            {!! $articulo->contenido !!}
        </div>
    </article>
@endsection
