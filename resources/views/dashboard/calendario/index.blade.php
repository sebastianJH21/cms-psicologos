@extends('dashboard.layout')

@section('titulo', 'Calendario')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Calendario'],
    ];
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $mesActual = (int) date('n') - 1;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/calendarjs/calendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/calendario.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                Calendario
            </h1>
            <p class="page-header__subtitle">Visualiza y gestiona tus citas por día, semana o mes.</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.citas.index') }}" class="btn btn--ghost">
                <i class="fa-solid fa-list" aria-hidden="true"></i>
                Lista de citas
            </a>
            <button type="button" class="btn btn--secondary" id="btn-nuevo-evento">
                <i class="fa-solid fa-star" aria-hidden="true"></i>
                Nuevo evento
            </button>
            <button type="button" class="btn btn--primary" id="btn-nueva-cita">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nueva cita
            </button>
        </div>
    </header>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1: NUEVO EVENTO EXTRA (formulario + Cancelar + Guardar)        --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="cal-modal" id="cal-evento-modal" role="dialog" aria-modal="true" hidden>
        <div class="cal-modal__backdrop" id="cal-evento-backdrop"></div>
        <div class="cal-modal__box">
            <header class="cal-modal__header">
                <h2 class="cal-modal__title"><i class="fa-solid fa-star" aria-hidden="true"></i> Nuevo evento extra</h2>
                <button type="button" class="cal-modal__close" id="cal-evento-close" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </header>
            <div class="cal-modal__body">
                <form id="cal-evento-form" class="form" novalidate>
                    <div class="form__field">
                        <label class="form__label" for="cal-ev-titulo">Título <span aria-hidden="true">*</span></label>
                        <input type="text" id="cal-ev-titulo" class="form__input" required maxlength="200">
                        <span class="form__error" id="cal-ev-error-titulo" hidden></span>
                    </div>
                    <div class="form__row">
                        <div class="form__field">
                            <label class="form__label" for="cal-ev-inicio">Inicio <span aria-hidden="true">*</span></label>
                            <input type="datetime-local" id="cal-ev-inicio" class="form__input" required>
                        </div>
                        <div class="form__field">
                            <label class="form__label" for="cal-ev-fin">Fin <span aria-hidden="true">*</span></label>
                            <input type="datetime-local" id="cal-ev-fin" class="form__input" required>
                        </div>
                    </div>
                    <div class="form__row">
                        <div class="form__field">
                            <label class="form__label" for="cal-ev-color">Color</label>
                            <input type="color" id="cal-ev-color" class="form__input" value="#9b59b6">
                        </div>
                        <div class="form__field">
                            <label class="form__label" for="cal-ev-ubicacion">Ubicación</label>
                            <input type="text" id="cal-ev-ubicacion" class="form__input" maxlength="200" placeholder="Ej: Estudio de radio">
                        </div>
                    </div>
                    <div class="form__field">
                        <label class="form__label" for="cal-ev-desc">Descripción</label>
                        <textarea id="cal-ev-desc" class="form__textarea" rows="3" maxlength="2000"></textarea>
                    </div>
                    <span class="form__error" id="cal-ev-error" hidden></span>
                </form>
            </div>
            <footer class="cal-modal__footer">
                <button type="button" class="btn btn--ghost" id="cal-evento-cancelar">Cancelar</button>
                <button type="button" class="btn btn--primary" id="cal-evento-guardar">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar evento
                </button>
            </footer>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 4: DETALLE DE EVENTO EXTRA (info + Borrar + Aceptar)          --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="cal-modal" id="cal-evento-detalle-modal" role="dialog" aria-modal="true" hidden>
        <div class="cal-modal__backdrop" id="cal-evento-detalle-backdrop"></div>
        <div class="cal-modal__box">
            <header class="cal-modal__header">
                <h2 class="cal-modal__title"><i class="fa-solid fa-star" aria-hidden="true"></i> Detalle del evento</h2>
                <button type="button" class="cal-modal__close" id="cal-evento-detalle-close" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </header>
            <div class="cal-modal__body">
                <dl class="cal-detalle">
                    <div class="cal-detalle__row">
                        <dt><i class="fa-solid fa-heading" aria-hidden="true"></i> Título</dt>
                        <dd id="cal-evd-titulo"></dd>
                    </div>
                    <div class="cal-detalle__row">
                        <dt><i class="fa-regular fa-clock" aria-hidden="true"></i> Fecha y hora</dt>
                        <dd id="cal-evd-fecha"></dd>
                    </div>
                    <div class="cal-detalle__row" id="cal-evd-ubicacion-row">
                        <dt><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Ubicación</dt>
                        <dd id="cal-evd-ubicacion"></dd>
                    </div>
                    <div class="cal-detalle__row" id="cal-evd-desc-row">
                        <dt><i class="fa-regular fa-comment" aria-hidden="true"></i> Descripción</dt>
                        <dd id="cal-evd-desc"></dd>
                    </div>
                </dl>
                <span class="form__error" id="cal-evd-error" hidden></span>
            </div>
            <footer class="cal-modal__footer">
                <button type="button" class="btn btn--danger" id="cal-evd-borrar">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    Borrar evento
                </button>
                <button type="button" class="btn btn--primary" id="cal-evd-aceptar">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                    Aceptar
                </button>
            </footer>
        </div>
    </div>

    <section class="panel panel--calendario">

        {{-- Toolbar --}}
        <div class="cal-toolbar">
            <div class="cal-toolbar__nav">
                <button type="button" class="btn btn--icon cal-nav-btn" id="cal-prev" aria-label="Anterior">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <strong class="cal-titulo-periodo" id="cal-titulo-periodo"></strong>
                <button type="button" class="btn btn--icon cal-nav-btn" id="cal-next" aria-label="Siguiente">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
            </div>

            <div class="cal-toolbar__mes">
                <select class="cal-select-mes" id="cal-select-mes" aria-label="Seleccionar mes">
                    @foreach($meses as $idx => $nombre)
                        <option value="{{ $idx }}" {{ $idx === $mesActual ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cal-toolbar__vistas" role="group" aria-label="Vista del calendario">
                <button type="button" class="cal-vista-btn" data-vista="day">Día</button>
                <button type="button" class="cal-vista-btn" data-vista="week">Semana</button>
                <button type="button" class="cal-vista-btn cal-vista-btn--active" data-vista="month">Mes</button>
            </div>
        </div>

        {{-- Área de renderizado --}}
        <div id="calendario-root"></div>

    </section>

    {{-- Leyenda --}}
    <section class="cal-leyenda" aria-label="Leyenda de colores">
        <span class="cal-leyenda__item"><span class="cal-leyenda__dot" style="background:#c98b1e"></span>Presencial confirmada</span>
        <span class="cal-leyenda__item"><span class="cal-leyenda__dot" style="background:#2f7ea1"></span>Online confirmada</span>
        <span class="cal-leyenda__item"><span class="cal-leyenda__dot" style="background:#2f8f5f"></span>Realizada</span>
        <span class="cal-leyenda__item"><span class="cal-leyenda__dot" style="background:#b03a2e"></span>No asistió</span>
        <span class="cal-leyenda__item"><span class="cal-leyenda__dot" style="background:#6b7180"></span>Pendiente</span>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2: NUEVA CITA (formulario + Cancelar + Guardar cita)           --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="cal-modal" id="cal-nueva-modal" role="dialog" aria-modal="true" hidden>
        <div class="cal-modal__backdrop" id="cal-nueva-backdrop"></div>
        <div class="cal-modal__box">
            <header class="cal-modal__header">
                <h2 class="cal-modal__title"><i class="fa-solid fa-calendar-plus" aria-hidden="true"></i> Nueva cita</h2>
                <button type="button" class="cal-modal__close" id="cal-nueva-close" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </header>
            <div class="cal-modal__body">
                <form id="cal-form" class="form" novalidate>
                    <input type="hidden" id="cal-paciente-id" name="paciente_id" value="">
                    <div class="form__row">
                        <div class="form__field">
                            <label class="form__label" for="cal-nombre">Paciente <span aria-hidden="true">*</span></label>
                            <input type="text" id="cal-nombre" name="nombre_provisional" class="form__input" required maxlength="150" placeholder="Nombre y apellidos" autocomplete="off">
                            <span class="form__error" id="cal-error-nombre" hidden></span>
                            <div class="cal-paciente-dropdown" id="cal-paciente-dropdown" hidden></div>
                            <div class="cal-paciente-seleccionado" id="cal-paciente-seleccionado" hidden></div>
                        </div>
                        <div class="form__field">
                            <label class="form__label" for="cal-telefono">Teléfono <span aria-hidden="true">*</span></label>
                            <input type="tel" id="cal-telefono" name="telefono_provisional" class="form__input" required maxlength="30" placeholder="+34 600 000 000">
                            <span class="form__error" id="cal-error-telefono" hidden></span>
                        </div>
                    </div>
                    <div class="form__row">
                        <div class="form__field">
                            <label class="form__label" for="cal-modalidad">Modalidad <span aria-hidden="true">*</span></label>
                            <select id="cal-modalidad" name="modalidad" class="form__select" required>
                                @foreach($modalidades as $k => $l)
                                    <option value="{{ $k }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form__field">
                            <label class="form__label" for="cal-estado">Estado</label>
                            <select id="cal-estado" name="estado" class="form__select">
                                @foreach($estados as $k => $l)
                                    <option value="{{ $k }}" {{ $k === 'confirmada' ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="cal-fecha-fin" name="fecha_fin">
                    <div class="form__field">
                        <label class="form__label" for="cal-fecha-inicio">Fecha <span aria-hidden="true">*</span></label>
                        <input type="datetime-local" id="cal-fecha-inicio" name="fecha_inicio" class="form__input form-input--locked cal-fecha-grande" required readonly>
                        <div class="cal-fecha-botones cal-fecha-botones--col">
                            <button type="button" class="btn btn--ghost btn--sm btn-huecos-anim cal-btn-grande" id="cal-disp-toggle">
                                <i class="fa-solid fa-calendar-check"></i> Ver huecos disponibles
                            </button>
                            <button type="button" class="btn btn--ghost btn--sm cal-btn-grande" id="cal-btn-manual">
                                <i class="fa-solid fa-pen"></i> Elegir fecha manualmente
                            </button>
                        </div>
                        <span class="form__error" id="cal-error-fecha" hidden></span>
                        <div class="cita-disponibilidad" id="cal-disp" hidden>
                            <div class="cita-disponibilidad__head">
                                <strong>Días con disponibilidad</strong>
                                <button type="button" class="btn btn--icon btn--sm" id="cal-disp-cerrar" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="cita-disponibilidad__dias" id="cal-disp-dias"></div>
                            <div class="cita-disponibilidad__slots" id="cal-disp-slots" hidden></div>
                            <p class="cita-disponibilidad__empty" id="cal-disp-empty" hidden>No hay huecos disponibles para esta modalidad.</p>
                        </div>
                    </div>
                    <div class="form__field">
                        <label class="form__label" for="cal-motivo">Motivo de consulta</label>
                        <textarea id="cal-motivo" name="motivo" class="form__textarea" rows="3" maxlength="1000" placeholder="Describe brevemente el motivo..."></textarea>
                    </div>
                    <span class="form__error" id="cal-error-solapamiento" hidden></span>
                </form>
            </div>
            <footer class="cal-modal__footer">
                <button type="button" class="btn btn--ghost" id="cal-nueva-cancelar">Cancelar</button>
                <button type="button" class="btn btn--primary" id="cal-nueva-guardar">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar cita
                </button>
            </footer>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3: DETALLE DE CITA (info + Ver ficha + Cancelar + Guardar estado) --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="cal-modal" id="cal-cita-detalle-modal" role="dialog" aria-modal="true" hidden>
        <div class="cal-modal__backdrop" id="cal-cita-detalle-backdrop"></div>
        <div class="cal-modal__box">
            <header class="cal-modal__header">
                <h2 class="cal-modal__title"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Detalle de cita</h2>
                <button type="button" class="cal-modal__close" id="cal-cita-detalle-close" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </header>
            <div class="cal-modal__body">
                <dl class="cal-detalle">
                    <div class="cal-detalle__row">
                        <dt><i class="fa-solid fa-user" aria-hidden="true"></i> Paciente</dt>
                        <dd id="cal-d-nombre"></dd>
                    </div>
                    <div class="cal-detalle__row">
                        <dt><i class="fa-solid fa-phone" aria-hidden="true"></i> Teléfono</dt>
                        <dd id="cal-d-telefono"></dd>
                    </div>
                    <div class="cal-detalle__row" id="cal-d-whatsapp-row" hidden>
                        <dt><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</dt>
                        <dd><a href="#" id="cal-d-whatsapp" target="_blank" rel="noopener" class="cal-detalle__wa">Confirmar cita por WhatsApp</a></dd>
                    </div>
                    <div class="cal-detalle__row">
                        <dt><i class="fa-regular fa-clock" aria-hidden="true"></i> Fecha y hora</dt>
                        <dd id="cal-d-fecha"></dd>
                    </div>
                    <div class="cal-detalle__row">
                        <dt><i class="fa-solid fa-tag" aria-hidden="true"></i> Modalidad</dt>
                        <dd id="cal-d-modalidad"></dd>
                    </div>
                    <div class="cal-detalle__row">
                        <dt><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Estado</dt>
                        <dd>
                            <select id="cal-d-estado-select" class="form__select cal-detalle__select">
                                @foreach($estados as $k => $l)
                                    <option value="{{ $k }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </dd>
                    </div>
                    <div class="cal-detalle__row" id="cal-d-motivo-row">
                            <dt></dt>
                            <p class="call-detalle__info">
                                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                Si quieres ver más detalles del paciente o editar o cancelar la cita, entra a "Ver ficha completa".
                            </p>
                    </div>
                    <!--
                        <div class="cal-detalle__row" id="cal-d-motivo-row">
                            <dt><i class="fa-regular fa-comment" aria-hidden="true"></i> Motivo</dt>
                            <dd id="cal-d-motivo"></dd>
                        </div>
                    -->
                </dl>
                <span class="form__error cal-detalle__error" id="cal-d-error" hidden></span>
                <span class="cal-detalle__ok" id="cal-d-ok"><i class="fa-solid fa-check"></i> Estado actualizado</span>
            </div>
            <footer class="cal-modal__footer">
                <a href="#" id="cal-link-detalle" class="btn btn--ghost">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    Ver ficha completa
                </a>
                <button type="button" class="btn btn--ghost" id="cal-cita-detalle-cancelar">Cancelar</button>
                <button type="button" class="btn btn--primary" id="cal-cita-detalle-guardar">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar estado
                </button>
            </footer>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/calendarjs/lemonade.min.js') }}"></script>
    <script src="{{ asset('vendor/calendarjs/calendar.min.js') }}"></script>
    <script>
        window.CalendarioConfig = {
            eventosUrl: '{{ route('dashboard.calendario.eventos') }}',
            crearUrl: '{{ route('dashboard.calendario.crear') }}',
            actualizarBase: '{{ url('panel-psicologa/calendario/citas') }}',
            eventoExtraUrl: '{{ route('dashboard.calendario.eventos-extra.store') }}',
            eventoExtraBase: '{{ url('panel-psicologa/calendario/eventos-extra') }}',
            citaBase: '{{ url('panel-psicologa/citas') }}',
            pacientesBuscarUrl: '{{ route('dashboard.pacientes.buscar') }}',
            duracionPresencial: {{ $duracionPresencial }},
            duracionOnline: {{ $duracionOnline }},
            psicologa: @json($psicologaNombre),
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        };
    </script>
    <script src="{{ asset('js/dashboard/calendario.js') }}" defer></script>
@endpush
