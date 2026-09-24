@extends('dashboard.layout')

@section('titulo', 'Redes sociales')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Configuración'],
        ['label' => 'Redes sociales'],
    ];

    $redInfo = [
        'facebook'  => ['icon' => 'fa-brands fa-facebook',   'label' => 'Facebook',   'placeholder' => 'https://facebook.com/tupagina'],
        'instagram' => ['icon' => 'fa-brands fa-instagram',  'label' => 'Instagram',  'placeholder' => 'https://instagram.com/tuusuario'],
        'linkedin'  => ['icon' => 'fa-brands fa-linkedin',   'label' => 'LinkedIn',   'placeholder' => 'https://linkedin.com/in/tuperfil'],
        'twitter'   => ['icon' => 'fa-brands fa-twitter',  'label' => 'X / Twitter','placeholder' => 'https://x.com/tuusuario'],
        'youtube'   => ['icon' => 'fa-brands fa-youtube',    'label' => 'YouTube',    'placeholder' => 'https://youtube.com/@tucanal'],
        'tiktok'    => ['icon' => 'fa-brands fa-tiktok',     'label' => 'TikTok',     'placeholder' => 'https://tiktok.com/@tuusuario'],
        'whatsapp'  => ['icon' => 'fa-brands fa-whatsapp',   'label' => 'WhatsApp',   'placeholder' => 'https://wa.me/346XXXXXXXX'],
    ];
@endphp

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-share-nodes" aria-hidden="true"></i>
                Redes sociales
            </h1>
            <p class="page-header__subtitle">
                Los enlaces que rellenes aparecerán como iconos en el footer de tu web pública.
            </p>
        </div>
    </header>

    <form method="POST" action="{{ route('dashboard.configuracion.redes-sociales.update') }}">
        @csrf
        @method('PUT')

        <section class="panel">
            <div class="redes-form-grid">
                @foreach ($redInfo as $key => $info)
                    <div class="form-field">
                        <label class="form-label redes-form-field__label" for="red-{{ $key }}">
                            <span class="redes-form-field__icon">
                                <i class="{{ $info['icon'] }}" aria-hidden="true"></i>
                            </span>
                            {{ $info['label'] }}
                        </label>
                        <input
                            type="text"
                            id="red-{{ $key }}"
                            name="{{ $key }}"
                            value="{{ old($key, $redes[$key]) }}"
                            placeholder="{{ $info['placeholder'] }}"
                            autocomplete="off"
                        >
                        @error($key)
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar redes sociales
                </button>
            </div>
        </section>
    </form>
@endsection
