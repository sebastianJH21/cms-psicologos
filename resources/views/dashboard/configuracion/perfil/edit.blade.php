@extends('dashboard.layout')

@section('titulo', 'Información pública')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Información pública'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Información pública</h1>
            <p class="page-header__subtitle">Datos que se mostrarán en tu web pública.</p>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('dashboard.configuracion.perfil.update') }}"
            class="cita-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="config-form__grid">
                <div class="form-field">
                    <label for="slogan">
                        <i class="fa-solid fa-quote-left" aria-hidden="true"></i>
                        Frase gancho / Eslogan
                    </label>
                    <input type="text" id="slogan" name="slogan" maxlength="200"
                        value="{{ old('slogan', $profile->slogan) }}"
                        placeholder="Ej: Acompañándote en tu camino de bienestar emocional">
                    @error('slogan')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="telefono_publico">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        Teléfono público
                    </label>
                    <input type="tel" id="telefono_publico" name="telefono_publico" maxlength="30"
                        value="{{ old('telefono_publico', $profile->telefono_publico) }}"
                        placeholder="+34 600 123 456">
                    @error('telefono_publico')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="email_publico">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        Email público
                    </label>
                    <input type="email" id="email_publico" name="email_publico" maxlength="150"
                        value="{{ old('email_publico', $profile->email_publico) }}"
                        placeholder="contacto@miconsulta.es">
                    @error('email_publico')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="numero_colegiado">
                        <i class="fa-solid fa-id-badge" aria-hidden="true"></i>
                        Número de colegiado/a
                    </label>
                    <input type="text" id="numero_colegiado" name="numero_colegiado" maxlength="50"
                        value="{{ old('numero_colegiado', $profile->numero_colegiado) }}"
                        placeholder="Ej: M-12345">
                    <small class="form-field__hint">Aparecerá en tu web pública y en los documentos generados.</small>
                    @error('numero_colegiado')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="direccion">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        Dirección de la consulta
                    </label>
                    <input type="text" id="direccion" name="direccion" maxlength="300"
                        value="{{ old('direccion', $profile->direccion) }}"
                        placeholder="Calle Ejemplo 12, 28001 Madrid">
                    @error('direccion')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="lat">
                        <i class="fa-solid fa-map-pin" aria-hidden="true"></i>
                        Latitud (opcional)
                    </label>
                    <input type="number" step="any" id="lat" name="lat"
                        value="{{ old('lat', $profile->lat) }}" placeholder="40.416775">
                    @error('lat')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="lng">
                        <i class="fa-solid fa-map-pin" aria-hidden="true"></i>
                        Longitud (opcional)
                    </label>
                    <input type="number" step="any" id="lng" name="lng"
                        value="{{ old('lng', $profile->lng) }}" placeholder="-3.703790">
                    <small class="form-field__hint">Sirve para ubicar la consulta en el mapa de Google.</small>
                    @error('lng')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="form-field">
                <label for="sobre_mi">
                    <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                    Sobre mí
                </label>
                <textarea id="sobre_mi" name="sobre_mi" data-jodit data-jodit-no-image rows="12">{{ old('sobre_mi', $profile->sobre_mi) }}</textarea>
                <small class="form-field__hint">Cuenta tu trayectoria, especialidades y enfoque profesional.</small>
                @error('sobre_mi')<small class="form-field__error">{{ $message }}</small>@enderror
            </div>

            <fieldset class="config-form__fieldset">
                <legend>
                    <i class="fa-solid fa-image" aria-hidden="true"></i>
                    Foto de la psicóloga
                </legend>

                @if ($profile->foto_path)
                    <div class="config-form__foto-actual">
                        <img src="{{ asset('storage/' . $profile->foto_path) }}" alt="Foto actual" class="config-form__foto-preview">
                    </div>
                @endif

                <div class="form-field">
                    <label for="foto">Subir nueva foto</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <small class="form-field__hint">Preferiblemente sin fondo (PNG con transparencia). Máx. 4 MB.</small>
                    @error('foto')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>

    @if ($profile->foto_path)
        <div class="form-actions">
            <form method="POST" action="{{ route('dashboard.configuracion.perfil.foto.destroy') }}"
                data-confirm="¿Eliminar la foto actual? Esta acción no se puede deshacer.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--ghost">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    Eliminar foto actual
                </button>
            </form>
        </div>
    @endif

    @include('dashboard.blog.partials.editor-jodit')
@endsection
