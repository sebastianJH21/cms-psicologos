@extends('dashboard.layout')

@section('titulo', 'Disponibilidad')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Disponibilidad'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/disponibilidad.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Disponibilidad y horarios</h1>
            <p class="page-header__subtitle">Define los huecos en los que aceptas citas online y presenciales.</p>
        </div>
    </header>

    <form method="POST" action="{{ route('dashboard.disponibilidad.update') }}" id="form-disponibilidad" class="dispo-form">
        @csrf
        @method('PUT')

        {{-- ── Configuración general ──────────────────────────────────────────── --}}
        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title">Configuración general</h2>
            </header>

            <div class="dispo-form__grid">
                <div class="form-field">
                    <label for="duracion_sesion_presencial_min">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        Duración sesión presencial (min)
                    </label>
                    <input type="number" id="duracion_sesion_presencial_min" name="duracion_sesion_presencial_min"
                           min="15" max="240" step="5"
                           value="{{ old('duracion_sesion_presencial_min', $duracionPresencial) }}" required>
                    @error('duracion_sesion_presencial_min')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="duracion_sesion_online_min">
                        <i class="fa-solid fa-video" aria-hidden="true"></i>
                        Duración sesión online (min)
                    </label>
                    <input type="number" id="duracion_sesion_online_min" name="duracion_sesion_online_min"
                           min="15" max="240" step="5"
                           value="{{ old('duracion_sesion_online_min', $duracionOnline) }}" required>
                    @error('duracion_sesion_online_min')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>

                <div class="form-field">
                    <label for="dias_adelante">
                        <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                        Días de antelación para nuevas citas
                    </label>
                    <input type="number" id="dias_adelante" name="dias_adelante"
                           min="7" max="365" step="1"
                           value="{{ old('dias_adelante', $diasAdelante) }}" required>
                    @error('dias_adelante')<small class="form-field__error">{{ $message }}</small>@enderror
                </div>
            </div>

            <p class="dispo-form__hint">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                Si cambias la duración o el descanso, los huecos se regenerarán al guardar. Revisa y vuelve a marcar los que quieras.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary" id="btn-guardar-config">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar configuración
                </button>
            </div>
        </section>

        {{-- ── Horarios mañana / tarde ────────────────────────────────────────── --}}
        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title">Franjas horarias</h2>
                <p class="panel__subtitle">Define el rango de horas de mañana y tarde en el que atiendes citas.</p>
            </header>

            <div class="dispo-horarios-row">
                <div class="dispo-horario-bloque">
                    <h3 class="dispo-horario-bloque__titulo">
                        <i class="fa-solid fa-sun" aria-hidden="true"></i>
                        Horario de mañana
                    </h3>
                    <div class="dispo-form__grid dispo-form__grid--2">
                        <div class="form-field">
                            <label for="hora_apertura_manana">Apertura</label>
                            <input type="time" id="hora_apertura_manana" name="hora_apertura_manana"
                                   value="{{ old('hora_apertura_manana', $horaAperturaManana) }}" required>
                            @error('hora_apertura_manana')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-field">
                            <label for="hora_cierre_manana">Cierre</label>
                            <input type="time" id="hora_cierre_manana" name="hora_cierre_manana"
                                   value="{{ old('hora_cierre_manana', $horaCierreManana) }}" required>
                            @error('hora_cierre_manana')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="dispo-horario-bloque">
                    <h3 class="dispo-horario-bloque__titulo">
                        <i class="fa-solid fa-cloud-sun" aria-hidden="true"></i>
                        Horario de tarde
                    </h3>
                    <div class="dispo-form__grid dispo-form__grid--2">
                        <div class="form-field">
                            <label for="hora_apertura_tarde">Apertura</label>
                            <input type="time" id="hora_apertura_tarde" name="hora_apertura_tarde"
                                   value="{{ old('hora_apertura_tarde', $horaAperturaTarde) }}" required>
                            @error('hora_apertura_tarde')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-field">
                            <label for="hora_cierre_tarde">Cierre</label>
                            <input type="time" id="hora_cierre_tarde" name="hora_cierre_tarde"
                                   value="{{ old('hora_cierre_tarde', $horaCierreTarde) }}" required>
                            @error('hora_cierre_tarde')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error solapamiento (desde backend) --}}
            @error('hora_apertura_tarde')
                <p class="dispo-form__error-solapamiento">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    {{ $message }}
                </p>
            @enderror

            {{-- Error solapamiento en tiempo real (JS) --}}
            <p class="dispo-form__error-solapamiento" id="error-solapamiento" hidden></p>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary" id="btn-guardar-dispo">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar configuración
                </button>
            </div>
        </section>

        {{-- ── Descanso entre sesiones ────────────────────────────────────────── --}}
        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title">Descanso entre sesiones</h2>
                <p class="panel__subtitle">Pausa automática tras cada sesión. Configúrala por separado para presencial y online.</p>
            </header>

            <div class="dispo-descanso__grid">
                <div class="dispo-descanso__col">
                    <div class="dispo-form__header-row">
                        <p class="dispo-descanso__modalidad-label">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            Presencial
                        </p>
                        <label class="switch">
                            <input type="checkbox" name="descanso_activo_presencial" id="descanso_activo_presencial" value="1"
                                   {{ old('descanso_activo_presencial', $descansoActivoPresencial) ? 'checked' : '' }}>
                            <span class="switch__slider"></span>
                        </label>
                    </div>
                    <div id="descanso-field-presencial" class="dispo-descanso__body" {{ !old('descanso_activo_presencial', $descansoActivoPresencial) ? 'hidden' : '' }}>
                        <div class="form-field">
                            <label for="descanso_min_presencial">Minutos de descanso</label>
                            <input type="number" id="descanso_min_presencial" name="descanso_min_presencial"
                                   min="5" max="120" step="5"
                                   value="{{ old('descanso_min_presencial', $descansoMinPresencial ?: 10) }}">
                            @error('descanso_min_presencial')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                        <p class="dispo-form__hint dispo-descanso__ejemplo" id="descanso-ejemplo-presencial"></p>
                    </div>
                </div>

                <div class="dispo-descanso__col">
                    <div class="dispo-form__header-row">
                        <p class="dispo-descanso__modalidad-label">
                            <i class="fa-solid fa-video" aria-hidden="true"></i>
                            Online
                        </p>
                        <label class="switch">
                            <input type="checkbox" name="descanso_activo_online" id="descanso_activo_online" value="1"
                                   {{ old('descanso_activo_online', $descansoActivoOnline) ? 'checked' : '' }}>
                            <span class="switch__slider"></span>
                        </label>
                    </div>
                    <div id="descanso-field-online" class="dispo-descanso__body" {{ !old('descanso_activo_online', $descansoActivoOnline) ? 'hidden' : '' }}>
                        <div class="form-field">
                            <label for="descanso_min_online">Minutos de descanso</label>
                            <input type="number" id="descanso_min_online" name="descanso_min_online"
                                   min="5" max="120" step="5"
                                   value="{{ old('descanso_min_online', $descansoMinOnline ?: 10) }}">
                            @error('descanso_min_online')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                        <p class="dispo-form__hint dispo-descanso__ejemplo" id="descanso-ejemplo-online"></p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Vacaciones ──────────────────────────────────────────────────────── --}}
        <div class="dispo-vacaciones-row">
            <section class="panel">
                <header class="panel__header dispo-form__header-row">
                    <div>
                        <h2 class="panel__title">Modo vacaciones</h2>
                        <p class="panel__subtitle">Pausa temporalmente las nuevas reservas públicas.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="modo_vacaciones" id="modo_vacaciones" value="1"
                               {{ old('modo_vacaciones', $modoVacaciones) ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </header>
                <div class="form-field">
                    <label for="mensaje_vacaciones">Mensaje opcional para los pacientes</label>
                    <textarea id="mensaje_vacaciones" name="mensaje_vacaciones" rows="3" maxlength="500"
                              placeholder="Estaré de vacaciones del 1 al 15 de agosto. Volveré con normalidad...">{{ old('mensaje_vacaciones', $mensajeVacaciones) }}</textarea>
                </div>
            </section>

            <section class="panel"
                data-vac-store="{{ route('dashboard.periodos-vacaciones.store') }}"
                data-vac-delete="{{ url('panel-psicologa/disponibilidad/periodos-vacaciones') }}"
                id="vac-panel">
                <header class="panel__header">
                    <h2 class="panel__title">
                        <i class="fa-solid fa-umbrella-beach" aria-hidden="true"></i>
                        Periodos de vacaciones
                    </h2>
                    <p class="panel__subtitle">Bloquea rangos de fechas para que no se puedan reservar citas en esos días.</p>
                </header>
                <div class="dispo-vac-add">
                    <div class="dispo-form__grid">
                        <div class="form-field">
                            <label for="vac-inicio">Fecha inicio</label>
                            <input type="date" id="vac-inicio" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-field">
                            <label for="vac-fin">Fecha fin</label>
                            <input type="date" id="vac-fin" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <button type="button" class="btn btn--primary btn--sm" id="vac-add-btn">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Añadir periodo
                    </button>
                    <p class="dispo-form__hint dispo-vac-error" id="vac-error" style="display:none; color: var(--color-danger);"></p>
                </div>
                <ul class="dispo-vac-list" id="vac-list">
                    @forelse ($periodos as $periodo)
                        <li class="dispo-vac-item" data-id="{{ $periodo->id }}">
                            <span>
                                <i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i>
                                {{ $periodo->fecha_inicio->format('d/m/Y') }} — {{ $periodo->fecha_fin->format('d/m/Y') }}
                            </span>
                            <button type="button" class="btn btn--ghost btn--sm dispo-vac-del"
                                    data-id="{{ $periodo->id }}" title="Eliminar periodo">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </li>
                    @empty
                        <li class="dispo-vac-item dispo-vac-item--empty" id="vac-empty">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            Sin periodos configurados
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        {{-- ── Tabs presencial / online (compartido entre ambos paneles de huecos) --}}
        <div class="dispo-tabs" role="tablist">
            <button type="button" class="dispo-tabs__btn dispo-tabs__btn--active" role="tab"
                    aria-selected="true" data-dispo-tab="presencial">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                Presencial
            </button>
            <button type="button" class="dispo-tabs__btn" role="tab"
                    aria-selected="false" data-dispo-tab="online">
                <i class="fa-solid fa-video" aria-hidden="true"></i>
                Online
            </button>
        </div>

        {{-- ── Huecos de mañana ───────────────────────────────────────────────── --}}
        <section class="panel dispo-panel-huecos">
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-sun" aria-hidden="true"></i>
                    Huecos disponibles · Mañana
                </h2>
                <p class="panel__subtitle">
                    {{ $horaAperturaManana }} – {{ $horaCierreManana }} · Pulsa los huecos para activarlos.
                </p>
            </header>

            @foreach (['presencial', 'online'] as $modalidad)
                @php
                    $slotsTemplate = $modalidad === 'presencial' ? $slotsPresencialManana : $slotsOnlineManana;
                    $periodo = 'manana';
                @endphp
                <div class="dispo-grid {{ $modalidad === 'presencial' ? 'dispo-grid--active' : '' }}"
                     data-modalidad="{{ $modalidad }}" data-periodo="{{ $periodo }}" role="tabpanel">
                    @if (count($slotsTemplate) === 0)
                        <div class="empty-state">
                            <i class="fa-regular fa-clock empty-state__icon" aria-hidden="true"></i>
                            <p class="empty-state__title">Sin franjas posibles</p>
                            <p class="empty-state__hint">Ajusta la duración o las horas de mañana para generar huecos.</p>
                        </div>
                    @else
                        @include('dashboard.disponibilidad.partials.slots-grid', [
                            'slotsTemplate' => $slotsTemplate,
                            'modalidad'     => $modalidad,
                            'ordenDias'     => $ordenDias,
                            'dias'          => $dias,
                            'seleccion'     => $seleccion,
                        ])
                    @endif
                </div>
            @endforeach
        </section>

        {{-- ── Huecos de tarde ────────────────────────────────────────────────── --}}
        <section class="panel dispo-panel-huecos">
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-cloud-sun" aria-hidden="true"></i>
                    Huecos disponibles · Tarde
                </h2>
                <p class="panel__subtitle">
                    {{ $horaAperturaTarde }} – {{ $horaCierreTarde }} · Pulsa los huecos para activarlos.
                </p>
            </header>

            @foreach (['presencial', 'online'] as $modalidad)
                @php
                    $slotsTemplate = $modalidad === 'presencial' ? $slotsPresencialTarde : $slotsOnlineTarde;
                    $periodo = 'tarde';
                @endphp
                <div class="dispo-grid {{ $modalidad === 'presencial' ? 'dispo-grid--active' : '' }}"
                     data-modalidad="{{ $modalidad }}" data-periodo="{{ $periodo }}" role="tabpanel">
                    @if (count($slotsTemplate) === 0)
                        <div class="empty-state">
                            <i class="fa-regular fa-clock empty-state__icon" aria-hidden="true"></i>
                            <p class="empty-state__title">Sin franjas posibles</p>
                            <p class="empty-state__hint">Ajusta la duración o las horas de tarde para generar huecos.</p>
                        </div>
                    @else
                        @include('dashboard.disponibilidad.partials.slots-grid', [
                            'slotsTemplate' => $slotsTemplate,
                            'modalidad'     => $modalidad,
                            'ordenDias'     => $ordenDias,
                            'dias'          => $dias,
                            'seleccion'     => $seleccion,
                        ])
                    @endif
                </div>
            @endforeach
        </section>

        <div class="form-actions">
            <a href="{{ route('dashboard.home') }}" class="btn btn--ghost">Volver</a>
            <button type="submit" class="btn btn--primary" id="btn-guardar-dispo-bottom">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                Guardar disponibilidad
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/disponibilidad.js') }}" defer></script>
@endpush
