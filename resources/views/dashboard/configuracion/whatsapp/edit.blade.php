@extends('dashboard.layout')

@section('titulo', 'Mensaje de WhatsApp')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Configuración'],
        ['label' => 'Mensaje de WhatsApp'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                Mensaje de WhatsApp
            </h1>
            <p class="page-header__subtitle">
                Personaliza el mensaje que se abre al pulsar "Confirmar por WhatsApp" en una cita. Puedes escribir emojis con normalidad.
            </p>
        </div>
    </header>

    <form method="POST" action="{{ route('dashboard.configuracion.whatsapp.update') }}" id="form-whatsapp-plantilla">
        @csrf
        @method('PUT')

        <section class="panel" data-whatsapp-grupo>
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-message" aria-hidden="true"></i>
                    Mensaje de confirmación de cita
                </h2>
            </header>
            <p class="panel__subtitle">
                Se usa en el botón "Confirmar por WhatsApp" de las citas.
            </p>

            <div class="placeholders-grid">
                @foreach ($variables as $variable => $descripcion)
                    <div class="placeholder-item">
                        <code class="placeholder-item__code">{{ $variable }}</code>
                        <span class="placeholder-item__desc">{{ $descripcion }}</span>
                    </div>
                @endforeach
            </div>

            <div class="form-field">
                <label class="form-label" for="plantilla_confirmacion">
                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                    Mensaje
                </label>
                <textarea
                    id="plantilla_confirmacion"
                    name="plantilla_confirmacion"
                    class="form-input"
                    rows="5"
                    maxlength="1000"
                    data-whatsapp-plantilla
                >{{ old('plantilla_confirmacion', $plantilla) }}</textarea>
                @error('plantilla_confirmacion') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <span class="form-label">
                    <i class="fa-regular fa-face-smile" aria-hidden="true"></i>
                    Emojis para tu mensaje
                </span>
                <p class="form-hint">Pulsa un emoji para añadirlo en el mensaje, en la posición donde tengas el cursor.</p>
                <div class="emoji-picker__grid">
                    @foreach ($emojis as $emoji)
                        <button type="button" class="emoji-picker__btn" data-emoji-btn aria-label="Insertar {{ $emoji }}">{{ $emoji }}</button>
                    @endforeach
                </div>
                <a href="{{ $enlaceMasEmojis }}" target="_blank" rel="noopener" class="emoji-picker__link">
                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    ¿Buscas más emojis? Ver lista completa
                </a>
            </div>

            <div class="form-field">
                <span class="form-label">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    Vista previa (con datos de ejemplo)
                </span>
                <div class="whatsapp-preview">
                    <p class="whatsapp-preview__bubble" data-whatsapp-preview>{{ $previsualizacion }}</p>
                </div>
            </div>
        </section>

        <section class="panel" data-whatsapp-grupo>
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-sack-dollar" aria-hidden="true"></i>
                    Mensaje de recordatorio de pago
                </h2>
            </header>
            <p class="panel__subtitle">
                Se usa en el botón "Recordar pago" de las citas confirmadas que aún no se han pagado.
            </p>

            <div class="placeholders-grid">
                @foreach ($variablesPago as $variable => $descripcion)
                    <div class="placeholder-item">
                        <code class="placeholder-item__code">{{ $variable }}</code>
                        <span class="placeholder-item__desc">{{ $descripcion }}</span>
                    </div>
                @endforeach
            </div>

            <div class="form-field">
                <label class="form-label" for="plantilla_recordatorio_pago">
                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                    Mensaje
                </label>
                <textarea
                    id="plantilla_recordatorio_pago"
                    name="plantilla_recordatorio_pago"
                    class="form-input"
                    rows="5"
                    maxlength="1000"
                    data-whatsapp-plantilla
                >{{ old('plantilla_recordatorio_pago', $plantillaPago) }}</textarea>
                @error('plantilla_recordatorio_pago') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <span class="form-label">
                    <i class="fa-regular fa-face-smile" aria-hidden="true"></i>
                    Emojis para tu mensaje
                </span>
                <p class="form-hint">Pulsa un emoji para añadirlo en el mensaje, en la posición donde tengas el cursor.</p>
                <div class="emoji-picker__grid">
                    @foreach ($emojis as $emoji)
                        <button type="button" class="emoji-picker__btn" data-emoji-btn aria-label="Insertar {{ $emoji }}">{{ $emoji }}</button>
                    @endforeach
                </div>
                <a href="{{ $enlaceMasEmojis }}" target="_blank" rel="noopener" class="emoji-picker__link">
                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    ¿Buscas más emojis? Ver lista completa
                </a>
            </div>

            <div class="form-field">
                <span class="form-label">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    Vista previa (con datos de ejemplo)
                </span>
                <div class="whatsapp-preview">
                    <p class="whatsapp-preview__bubble" data-whatsapp-preview>{{ $previsualizacionPago }}</p>
                </div>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                Guardar plantillas
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            window.WhatsappPreviewDatos = @json($datosPrevisualizacion);
            window.WhatsappPreviewDatosPago = @json($datosPrevisualizacionPago);
        </script>
        <script src="{{ asset('js/dashboard/whatsapp-plantilla.js') }}" defer></script>
    @endpush
@endsection
