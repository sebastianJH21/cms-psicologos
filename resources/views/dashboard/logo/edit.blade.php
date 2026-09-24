@extends('dashboard.layout')

@section('titulo', 'Logo y favicon')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Logo'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/logo.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-image" aria-hidden="true"></i>
                Logo y favicon
            </h1>
            <p class="page-header__subtitle">
                Sube una imagen propia o elige un icono. Se mostrará junto al nombre en la web pública y como favicon del navegador.
            </p>
        </div>
    </header>

    @if (session('success'))
        <div class="alert alert--success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert--error">
            <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
        </div>
    @endif

    <section class="panel logo-page">
        <div class="logo-page__preview-card">
            <div class="logo-page__preview-label">Previsualización</div>
            <div class="logo-page__preview">
                @if (!empty($logo['path']))
                    <img src="{{ asset('storage/' . $logo['path']) }}" alt="Logo actual">
                @elseif (!empty($logo['icon']))
                    <i class="fa-solid {{ $logo['icon'] }}"></i>
                @else
                    <i class="fa-solid fa-image logo-page__placeholder"></i>
                @endif
            </div>
            <p class="logo-page__preview-note">
                @if (!empty($logo['path']))
                    Imagen personalizada
                @elseif (!empty($logo['icon']))
                    Icono <code>{{ $logo['icon'] }}</code>
                @else
                    Sin logo configurado
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('dashboard.logo.update') }}" enctype="multipart/form-data" class="logo-page__form">
            @csrf
            @method('PUT')

            <div class="logo-page__modes">
                <label class="logo-page__mode">
                    <input type="radio" name="modo_logo" value="imagen" {{ !empty($logo['path']) ? 'checked' : '' }}>
                    <span class="logo-page__mode-card">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <strong>Subir imagen</strong>
                        <small>JPG, PNG, SVG o WebP · máx. 2 MB</small>
                    </span>
                </label>
                <label class="logo-page__mode">
                    <input type="radio" name="modo_logo" value="icono" {{ empty($logo['path']) && !empty($logo['icon']) ? 'checked' : '' }}>
                    <span class="logo-page__mode-card">
                        <i class="fa-solid fa-icons"></i>
                        <strong>Elegir icono</strong>
                        <small>Selección de iconos relacionados con bienestar</small>
                    </span>
                </label>
                <label class="logo-page__mode">
                    <input type="radio" name="modo_logo" value="ninguno" {{ empty($logo['path']) && empty($logo['icon']) ? 'checked' : '' }}>
                    <span class="logo-page__mode-card">
                        <i class="fa-solid fa-ban"></i>
                        <strong>Sin logo</strong>
                        <small>Solo mostrar el nombre en la web</small>
                    </span>
                </label>
            </div>

            <div class="logo-page__group" data-show-for="imagen">
                <label class="logo-page__label" for="logo_image">Subir imagen</label>
                <input type="file" id="logo_image" name="logo_image" accept="image/*" class="logo-page__file">
                <p class="logo-page__hint">Recomendado: imagen cuadrada con fondo transparente (PNG o SVG).</p>
            </div>

            <div class="logo-page__group" data-show-for="icono">
                <label class="logo-page__label">Selecciona un icono</label>
                <div class="logo-page__icons">
                    @foreach ($iconos as $ic)
                        <label class="logo-page__icon">
                            <input type="radio" name="logo_icon" value="{{ $ic }}" {{ ($logo['icon'] ?? '') === $ic ? 'checked' : '' }}>
                            <span><i class="fa-solid {{ $ic }}"></i></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard.temas.index') }}" class="btn btn--ghost">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Temas
                </a>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar logo
                </button>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/logo.js') }}" defer></script>
@endpush
