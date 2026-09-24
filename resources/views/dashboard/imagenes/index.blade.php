@extends('dashboard.layout')

@section('titulo', 'Imágenes públicas')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Imágenes'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/imagenes.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-images" aria-hidden="true"></i>
                Imágenes públicas
            </h1>
            <p class="page-header__subtitle">
                Personaliza las imágenes que aparecen en tu web. Tema activo:
                <strong>{{ $theme['name'] ?? $slug }}</strong>
            </p>
        </div>
    </header>

    <div class="imagenes-grid">
        @foreach ($items as $item)
            <article class="imagen-slot panel">
                <div class="imagen-slot__preview">
                    @if ($item['url'])
                        <img
                            src="{{ $item['url'] }}"
                            alt="{{ $item['label'] }}"
                            class="imagen-slot__img"
                            loading="lazy"
                        >
                    @else
                        <div class="imagen-slot__placeholder">
                            <i class="fa-regular fa-image" aria-hidden="true"></i>
                            <span>Sin imagen</span>
                        </div>
                    @endif
                    @if ($item['has_override'])
                        <span class="imagen-slot__badge">
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                            Personalizada
                        </span>
                    @endif
                </div>

                <div class="imagen-slot__info">
                    <h3 class="imagen-slot__label">{{ $item['label'] }}</h3>
                    @if ($item['hint'])
                        <p class="imagen-slot__hint">{{ $item['hint'] }}</p>
                    @endif
                </div>

                <div class="imagen-slot__actions">
                    <form
                        method="POST"
                        action="{{ route('dashboard.imagenes.update', $item['slot']) }}"
                        enctype="multipart/form-data"
                        class="imagen-slot__upload-form"
                    >
                        @csrf
                        <label class="btn btn--primary btn--sm imagen-slot__upload-label">
                            <i class="fa-solid fa-upload" aria-hidden="true"></i>
                            {{ $item['has_override'] ? 'Cambiar imagen' : 'Subir imagen' }}
                            <input
                                type="file"
                                name="imagen"
                                accept="image/jpeg,image/png,image/webp"
                                class="imagen-slot__file-input"
                                aria-label="Subir imagen para {{ $item['label'] }}"
                            >
                        </label>
                        <button type="submit" class="imagen-slot__upload-btn" hidden aria-hidden="true">Subir</button>
                    </form>

                    @if ($item['has_override'])
                        <form
                            method="POST"
                            action="{{ route('dashboard.imagenes.restore', $item['slot']) }}"
                            data-confirm="¿Restaurar la imagen original del tema?"
                        >
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">
                                <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                                Restaurar original
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/imagenes.js') }}" defer></script>
@endpush
