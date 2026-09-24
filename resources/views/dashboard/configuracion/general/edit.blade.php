@extends('dashboard.layout')

@section('titulo', 'Configuración general')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Configuración'],
        ['label' => 'General'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion-general.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/dashboard/configuracion-general.js') }}" defer></script>
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                Configuración general
            </h1>
            <p class="page-header__subtitle">Activa o desactiva las secciones de tu web pública. Los cambios se guardan al instante.</p>
        </div>
    </header>

    <section class="panel">
        <h2 class="panel__title">
            <i class="fa-solid fa-toggle-on" aria-hidden="true"></i>
            Funcionalidades de la web pública
        </h2>
        <p class="panel__desc">Activa o desactiva las diferentes secciones que verán tus pacientes en la web.</p>

        <div class="features-list" data-toggle-url="{{ route('dashboard.configuracion.general.feature.toggle') }}">
            @php
                $featureLabels = [
                    'blog'      => ['icon' => 'fa-newspaper',       'label' => 'Blog',                 'desc' => 'Sección de artículos y contenido publicados en tu web.'],
                    'reservas'  => ['icon' => 'fa-calendar-check',  'label' => 'Sistema de reservas',  'desc' => 'Permite a tus pacientes reservar cita online.'],
                    'faq'       => ['icon' => 'fa-circle-question', 'label' => 'Preguntas frecuentes', 'desc' => 'Muestra un listado de preguntas y respuestas habituales.'],
                    'servicios' => ['icon' => 'fa-briefcase',       'label' => 'Servicios',            'desc' => 'Muestra los servicios que ofreces en tu web.'],
                    'sobre_mi'  => ['icon' => 'fa-user',            'label' => 'Sobre mí',             'desc' => 'Sección de presentación e información personal.'],
                ];
            @endphp

            @foreach ($featureLabels as $key => $info)
                <div class="feature-item" data-feature="{{ $key }}">
                    <div class="feature-item__icon feature-item__icon--{{ $key }}">
                        <i class="fa-solid {{ $info['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <div class="feature-item__info">
                        <span class="feature-item__label">{{ $info['label'] }}</span>
                        <span class="feature-item__desc">{{ $info['desc'] }}</span>
                    </div>
                    <div class="feature-item__actions">
                        <span class="feature-item__status" aria-live="polite"></span>
                        <label class="switch" aria-label="Activar {{ $info['label'] }}">
                            <input
                                type="checkbox"
                                data-feature="{{ $key }}"
                                value="1"
                                @checked($features[$key] ?? true)
                            >
                            <span class="switch__slider"></span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
