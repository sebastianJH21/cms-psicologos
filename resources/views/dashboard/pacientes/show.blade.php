@extends('dashboard.layout')

@section('titulo', $paciente->nombre_completo)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo],
    ];

    $generoBadge = match ($paciente->genero) {
        'mujer'             => 'info',
        'hombre'            => 'success',
        'otro'              => 'warning',
        'prefiero_no_decir' => 'default',
        default             => 'default',
    };

    $citaErrors = $errors->hasAny(['nombre_provisional', 'telefono_provisional', 'email_provisional', 'modalidad', 'fecha_inicio', 'fecha_fin', 'motivo', 'estado']);
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/pacientes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/historias.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">{{ $paciente->nombre_completo }}</h1>
            <p class="page-header__subtitle">
                <i class="fa-solid fa-phone" aria-hidden="true"></i> {{ $paciente->telefono }}
                @if ($paciente->email)
                    &nbsp;&middot;&nbsp; <i class="fa-solid fa-envelope" aria-hidden="true"></i> {{ $paciente->email }}
                @endif
            </p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.pacientes.historias.index', $paciente) }}" class="btn btn--ghost">
                <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                Historia clínica
                @if ($totalHistorias > 0)
                    <span class="badge badge--info" style="margin-left:0.4rem">{{ $totalHistorias }}</span>
                @endif
            </a>
            <a href="{{ route('dashboard.pacientes.proteccion-datos', $paciente) }}"
                class="btn btn--ghost" target="_blank" rel="noopener"
                title="Descargar PDF de protección de datos">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                PDF protección
            </a>
            <button type="button" class="btn btn--ghost" id="btn-nueva-cita">
                <i class="fa-solid fa-calendar-plus" aria-hidden="true"></i>
                Nueva cita
            </button>
            <a href="{{ route('dashboard.pacientes.edit', $paciente) }}" class="btn btn--primary">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                Editar
            </a>
        </div>
    </header>

    <div class="paciente-show">

        {{-- ── Datos personales — ancho completo ───────────────────────────── --}}
        <section class="panel paciente-show__full">
            <header class="panel__header">
                <h2 class="panel__title">Datos personales</h2>
                <span class="badge badge--{{ $paciente->origen === 'publica' ? 'success' : 'warning' }}">
                    {{ $paciente->origen === 'publica' ? 'Reserva pública' : 'Manual' }}
                </span>
            </header>

            <div class="paciente-datos">
                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--primary">
                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Nombre</span>
                        <span class="paciente-dato__value">{{ $paciente->nombre }}</span>
                    </div>
                </div>

                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--primary">
                        <i class="fa-solid fa-id-card" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Apellidos</span>
                        <span class="paciente-dato__value">{{ $paciente->apellidos ?: '—' }}</span>
                    </div>
                </div>

                @if ($paciente->dni)
                    <div class="paciente-dato">
                        <span class="paciente-dato__icon paciente-dato__icon--muted">
                            <i class="fa-solid fa-id-badge" aria-hidden="true"></i>
                        </span>
                        <div class="paciente-dato__body">
                            <span class="paciente-dato__label">DNI / NIE</span>
                            <span class="paciente-dato__value">{{ $paciente->dni }}</span>
                        </div>
                    </div>
                @endif

                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--success">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Teléfono</span>
                        <span class="paciente-dato__value">
                            @if ($paciente->telefono)
                                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $paciente->telefono) }}" class="paciente-dato__link">{{ $paciente->telefono }}</a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                @php $waPaciente = preg_replace('/\D+/', '', (string) $paciente->telefono); @endphp
                @if ($waPaciente)
                    <div class="paciente-dato">
                        <span class="paciente-dato__icon paciente-dato__icon--success">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                        </span>
                        <div class="paciente-dato__body">
                            <span class="paciente-dato__label">WhatsApp</span>
                            <span class="paciente-dato__value">
                                <a href="https://wa.me/{{ $waPaciente }}" target="_blank" rel="noopener" class="paciente-dato__link paciente-dato__link--wa">Escribir por WhatsApp</a>
                            </span>
                        </div>
                    </div>
                @endif

                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--info">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Email</span>
                        <span class="paciente-dato__value">
                            @if ($paciente->email)
                                <a href="mailto:{{ $paciente->email }}" class="paciente-dato__link">{{ $paciente->email }}</a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--warning">
                        <i class="fa-solid fa-cake-candles" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Fecha de nacimiento</span>
                        <span class="paciente-dato__value">
                            {{ $paciente->fecha_nacimiento?->format('d/m/Y') ?: '—' }}
                            @if ($paciente->edad)
                                <span class="badge badge--info" style="margin-left:0.6rem">{{ $paciente->edad }} años</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--{{ $generoBadge === 'default' ? 'muted' : $generoBadge }}">
                        <i class="fa-solid fa-venus-mars" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Género</span>
                        <span class="paciente-dato__value">
                            @if ($paciente->genero_label)
                                <span class="badge badge--{{ $generoBadge }}">{{ $paciente->genero_label }}</span>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>

                @if ($paciente->direccion)
                    <div class="paciente-dato">
                        <span class="paciente-dato__icon paciente-dato__icon--muted">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        </span>
                        <div class="paciente-dato__body">
                            <span class="paciente-dato__label">Dirección</span>
                            <span class="paciente-dato__value">{{ $paciente->direccion }}</span>
                        </div>
                    </div>
                @endif

                <div class="paciente-dato">
                    <span class="paciente-dato__icon paciente-dato__icon--muted">
                        <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                    </span>
                    <div class="paciente-dato__body">
                        <span class="paciente-dato__label">Alta en el sistema</span>
                        <span class="paciente-dato__value">{{ $paciente->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Motivo inicial y Notas — lado a lado ─────────────────────────── --}}
        @if ($paciente->motivo_inicial)
            <section class="panel">
                <header class="panel__header">
                    <h2 class="panel__title">
                        <i class="fa-solid fa-comment-medical" aria-hidden="true"></i>
                        Motivo inicial
                    </h2>
                </header>
                <p class="cita-show__text">{{ $paciente->motivo_inicial }}</p>
            </section>
        @endif

        @if ($paciente->notas)
            <section class="panel">
                <header class="panel__header">
                    <h2 class="panel__title">
                        <i class="fa-solid fa-note-sticky" aria-hidden="true"></i>
                        Notas internas
                    </h2>
                </header>
                <p class="cita-show__text cita-show__text--muted">{{ $paciente->notas }}</p>
            </section>
        @endif

        {{-- ── Historia clínica — ancho completo ──────────────────────────── --}}
        <section class="panel paciente-show__full">
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-notes-medical" aria-hidden="true"></i>
                    Historia clínica
                </h2>
                <div style="display:flex;gap:0.8rem;align-items:center">
                    <a href="{{ route('dashboard.pacientes.historias.create', $paciente) }}" class="btn btn--ghost btn--sm">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Nueva entrada
                    </a>
                    @if ($totalHistorias > 0)
                        <a href="{{ route('dashboard.pacientes.historias.index', $paciente) }}" class="btn btn--ghost btn--sm">
                            Ver todas ({{ $totalHistorias }})
                        </a>
                    @endif
                </div>
            </header>

            @if ($historiasRecientes->isEmpty())
                <div class="empty-state empty-state--inline">
                    <i class="fa-regular fa-folder-open empty-state__icon" aria-hidden="true"></i>
                    <p class="empty-state__title">Sin entradas en la historia</p>
                    <p class="empty-state__hint">Registra las notas de cada sesión aquí.</p>
                </div>
            @else
                <div class="historia-resumen-timeline">
                    @foreach ($historiasRecientes as $h)
                        <a href="{{ route('dashboard.pacientes.historias.show', [$paciente, $h]) }}"
                            class="historia-resumen-item">
                            <div class="historia-resumen-item__fecha">
                                <span class="historia-resumen-item__dia">{{ $h->fecha_sesion->format('d') }}</span>
                                <span class="historia-resumen-item__mes">{{ strtoupper($h->fecha_sesion->translatedFormat('M')) }}</span>
                            </div>
                            <div class="historia-resumen-item__body">
                                <p class="historia-resumen-item__titulo">{{ $h->titulo_mostrado }}</p>
                                <p class="historia-resumen-item__excerpt">
                                    {{ Str::limit(strip_tags($h->contenido), 100) }}
                                    @if ($h->archivos_count > 0)
                                        &nbsp;<i class="fa-solid fa-paperclip" aria-hidden="true"></i> {{ $h->archivos_count }}
                                    @endif
                                </p>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color:var(--color-text-subtle);align-self:center" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── Historial de citas — ancho completo ─────────────────────────── --}}
        <section class="panel paciente-show__full">
            <header class="panel__header">
                <h2 class="panel__title">Historial de citas</h2>
                <a href="{{ route('dashboard.pacientes.citas', $paciente) }}" class="btn btn--ghost btn--sm">
                    Ver todas ({{ $totalCitas }})
                </a>
            </header>

            @if ($paciente->citas->isEmpty())
                <div class="empty-state empty-state--inline">
                    <i class="fa-regular fa-calendar empty-state__icon" aria-hidden="true"></i>
                    <p class="empty-state__title">Sin citas registradas</p>
                    <p class="empty-state__hint">Cuando agendes una cita aparecerá aquí.</p>
                </div>
            @else
                <div class="data-table__wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Modalidad</th>
                                <th scope="col">Estado</th>
                                <th scope="col" class="data-table__actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paciente->citas as $cita)
                                <tr>
                                    <td>
                                        <strong>{{ $cita->fecha_inicio->format('d/m/Y') }}</strong>
                                        <small class="data-table__sub">{{ $cita->fecha_inicio->format('H:i') }} – {{ $cita->fecha_fin->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">
                                            {{ $cita->modalidad_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $estadoClase = match ($cita->estado) {
                                                'confirmada' => 'success',
                                                'realizada'  => 'info',
                                                'cancelada'  => 'danger',
                                                'no_asistio' => 'warning',
                                                default      => 'info',
                                            };
                                        @endphp
                                        <span class="badge badge--{{ $estadoClase }}">{{ $cita->estado_label }}</span>
                                    </td>
                                    <td class="data-table__actions">
                                        <a href="{{ route('dashboard.citas.show', $cita) }}" class="btn btn--icon" aria-label="Ver">
                                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </div>

    {{-- ── Modal nueva cita ───────────────────────────────────────────────────── --}}
    <div class="modal" id="modal-nueva-cita" hidden aria-modal="true" role="dialog" aria-labelledby="modal-cita-titulo">
        <div class="modal__backdrop"></div>
        <div class="modal__dialog modal__dialog--wide">
            <div class="modal__header">
                <h2 class="modal__title" id="modal-cita-titulo">
                    <i class="fa-solid fa-calendar-plus" aria-hidden="true"></i>
                    Nueva cita — {{ $paciente->nombre_completo }}
                </h2>
                <button type="button" class="modal__close" data-modal-cita-close aria-label="Cerrar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('dashboard.citas.store') }}">
                @csrf
                <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                <input type="hidden" name="nombre_provisional" value="{{ $paciente->nombre_completo }}">
                <input type="hidden" name="telefono_provisional" value="{{ $paciente->telefono }}">
                <input type="hidden" name="email_provisional" value="{{ $paciente->email }}">
                <div class="modal__body cita-form">
                    {{-- Datos del paciente — solo lectura --}}
                    <div class="form-field">
                        <label>Datos del paciente</label>
                        <p class="form-field__hint" style="margin-bottom:0.8rem">Solo editables en la <a href="{{ route('dashboard.pacientes.edit', $paciente) }}" class="cita-show__link">ficha del paciente</a>.</p>
                        <div class="cita-datos-badges">
                            <span class="cita-dato-badge cita-dato-badge--plain">
                                <i class="fa-solid fa-user" aria-hidden="true"></i> {{ $paciente->nombre_completo }}
                            </span>
                            @if ($paciente->telefono)
                                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $paciente->telefono) }}" class="cita-dato-badge">
                                    <i class="fa-solid fa-phone" aria-hidden="true"></i> {{ $paciente->telefono }}
                                </a>
                            @endif
                            @if ($paciente->email)
                                <a href="mailto:{{ $paciente->email }}" class="cita-dato-badge">
                                    <i class="fa-solid fa-envelope" aria-hidden="true"></i> {{ $paciente->email }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="cita-form__grid">
                        <div class="form-field">
                            <label for="mc-modalidad">Modalidad *</label>
                            <select id="mc-modalidad" name="modalidad" required>
                                @foreach ($modalidades as $k => $l)
                                    <option value="{{ $k }}" {{ old('modalidad') === $k ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            @error('modalidad')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-field">
                            <label for="mc-fecha">Fecha y hora *</label>
                            <input type="datetime-local" id="mc-fecha" name="fecha_inicio" required
                                value="{{ old('fecha_inicio', now()->addHour()->format('Y-m-d\TH:00')) }}">
                            <button type="button" class="btn btn--ghost btn--sm" id="mc-btn-disp" style="margin-top:0.6rem;">
                                <i class="fa-solid fa-calendar-check"></i> Ver huecos disponibles
                            </button>
                            @error('fecha_inicio')<small class="form-field__error">{{ $message }}</small>@enderror
                            @error('fecha_fin')<small class="form-field__error">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-field">
                            <label for="mc-estado">Estado</label>
                            <select id="mc-estado" name="estado">
                                <option value="confirmada" {{ old('estado', 'confirmada') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="pendiente" {{ old('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-field" style="margin-top:1.6rem">
                        <label for="mc-motivo">Motivo de la consulta</label>
                        <textarea id="mc-motivo" name="motivo" rows="3" maxlength="1000">{{ old('motivo') }}</textarea>
                        @error('motivo')<small class="form-field__error">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="modal__footer">
                    <button type="button" class="btn btn--ghost" data-modal-cita-close>Cancelar</button>
                    <button type="submit" class="btn btn--primary">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                        Crear cita
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de slots disponibles para el modal de nueva cita --}}
    <div class="cita-slots-modal" id="mc-slots-modal" role="dialog" aria-modal="true" hidden>
        <div class="cita-slots-modal__backdrop" id="mc-slots-modal-backdrop"></div>
        <div class="cita-slots-modal__box">
            <header class="cita-slots-modal__header">
                <h3><i class="fa-solid fa-calendar-check"></i> Selecciona un hueco disponible</h3>
                <button type="button" class="btn btn--icon" id="mc-slots-close" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <div class="cita-slots-modal__body">
                <div class="cita-slots-modal__cal">
                    <div class="cita-slots-modal__cal-head">
                        <button type="button" class="btn btn--icon" id="mc-slots-prev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
                        <strong id="mc-slots-titulo">—</strong>
                        <button type="button" class="btn btn--icon" id="mc-slots-next" aria-label="Siguiente"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                    <div class="cita-slots-modal__cal-grid" id="mc-slots-grid"></div>
                </div>
                <div class="cita-slots-modal__slots">
                    <p class="cita-slots-modal__slots-hint" id="mc-slots-hint">Selecciona un día con disponibilidad.</p>
                    <div class="cita-slots-modal__slots-list" id="mc-slots-list"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
@endpush

@push('scripts')
<script>
    (() => {
        const modal = document.getElementById('modal-nueva-cita');
        if (!modal) return;

        const openBtn = document.getElementById('btn-nueva-cita');
        const closers = modal.querySelectorAll('[data-modal-cita-close]');
        const backdrop = modal.querySelector('.modal__backdrop');
        let lastFocus = null;

        const open = () => {
            modal.hidden = false;
            lastFocus = document.activeElement;
            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) firstInput.focus();
        };

        const close = () => {
            modal.hidden = true;
            if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        };

        openBtn?.addEventListener('click', open);
        closers.forEach(el => el.addEventListener('click', close));
        backdrop?.addEventListener('click', close);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !modal.hidden) close();
        });

        @if ($citaErrors)
        open();
        @endif
    })();

    // ── Picker de slots disponibles para el modal del paciente ─────────────
    (() => {
        const btnAbrir = document.getElementById('mc-btn-disp');
        if (!btnAbrir) return;

        const slotsModal = document.getElementById('mc-slots-modal');
        const slotsBackdrop = document.getElementById('mc-slots-modal-backdrop');
        const btnClose = document.getElementById('mc-slots-close');
        const titulo = document.getElementById('mc-slots-titulo');
        const grid = document.getElementById('mc-slots-grid');
        const lista = document.getElementById('mc-slots-list');
        const hint = document.getElementById('mc-slots-hint');
        const btnPrev = document.getElementById('mc-slots-prev');
        const btnNext = document.getElementById('mc-slots-next');
        const inputFecha = document.getElementById('mc-fecha');
        const selectModalidad = document.getElementById('mc-modalidad');

        const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        const DIAS = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
        const pad = (n) => String(n).padStart(2, '0');
        const hoy = new Date();
        let mesVista = hoy.getMonth();
        let anyoVista = hoy.getFullYear();
        let diasDisponibles = new Set();
        let diaSeleccionado = null;

        const limpiar = (el) => { while (el && el.firstChild) el.removeChild(el.firstChild); };

        const cerrar = () => { slotsModal.hidden = true; };
        const abrir = () => {
            const v = inputFecha?.value;
            if (v) {
                const d = new Date(v);
                if (!isNaN(d)) {
                    mesVista = d.getMonth();
                    anyoVista = d.getFullYear();
                    diaSeleccionado = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
                }
            }
            slotsModal.hidden = false;
            cargarDiasYRenderizar();
            if (diaSeleccionado) setTimeout(() => cargarSlotsDia(diaSeleccionado), 50);
        };

        const cargarDiasYRenderizar = async () => {
            const modalidad = selectModalidad?.value || 'presencial';
            diasDisponibles = new Set();
            try {
                const res = await fetch(`/reservas/dias?modalidad=${encodeURIComponent(modalidad)}`);
                if (res.ok) {
                    const data = await res.json();
                    (data.dias || []).forEach((d) => diasDisponibles.add(d));
                }
            } catch {}
            renderizarCalendario();
        };

        const renderizarCalendario = () => {
            titulo.textContent = `${MESES[mesVista]} ${anyoVista}`;
            limpiar(grid);
            DIAS.forEach((d) => {
                const cab = document.createElement('div');
                cab.className = 'cal-dia-cab';
                cab.textContent = d;
                grid.appendChild(cab);
            });
            const primerDia = new Date(anyoVista, mesVista, 1);
            const ultimoDia = new Date(anyoVista, mesVista + 1, 0);
            const inicio = (primerDia.getDay() + 6) % 7;
            const total = Math.ceil((inicio + ultimoDia.getDate()) / 7) * 7;
            for (let i = 0; i < total; i++) {
                const cel = document.createElement('div');
                cel.className = 'cal-dia';
                const numDia = i - inicio + 1;
                if (numDia < 1 || numDia > ultimoDia.getDate()) {
                    cel.classList.add('cal-dia--fuera');
                    grid.appendChild(cel);
                    continue;
                }
                const fechaStr = `${anyoVista}-${pad(mesVista + 1)}-${pad(numDia)}`;
                cel.textContent = numDia;
                if (diasDisponibles.has(fechaStr)) {
                    cel.classList.add('cal-dia--disponible');
                    if (diaSeleccionado === fechaStr) cel.classList.add('cal-dia--seleccionado');
                    cel.addEventListener('click', () => {
                        diaSeleccionado = fechaStr;
                        renderizarCalendario();
                        cargarSlotsDia(fechaStr);
                    });
                }
                grid.appendChild(cel);
            }
        };

        const cargarSlotsDia = async (fecha) => {
            limpiar(lista);
            hint.textContent = 'Cargando huecos...';
            const modalidad = selectModalidad?.value || 'presencial';
            try {
                const res = await fetch(`/reservas/slots?modalidad=${encodeURIComponent(modalidad)}&fecha=${encodeURIComponent(fecha)}`);
                const data = await res.json();
                const slots = data.slots || [];
                if (!slots.length) {
                    hint.textContent = 'No hay huecos libres para este día.';
                    return;
                }
                hint.textContent = `${slots.length} ${slots.length === 1 ? 'hueco' : 'huecos'} disponibles:`;
                slots.forEach((s) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'slot-btn';
                    btn.textContent = s.hora;
                    btn.addEventListener('click', () => {
                        const dt = new Date(s.inicio_iso);
                        inputFecha.value = `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
                        cerrar();
                    });
                    lista.appendChild(btn);
                });
            } catch {
                hint.textContent = 'No se pudieron cargar los huecos.';
            }
        };

        btnAbrir.addEventListener('click', (e) => { e.preventDefault(); abrir(); });
        btnClose.addEventListener('click', cerrar);
        slotsBackdrop.addEventListener('click', cerrar);
        btnPrev.addEventListener('click', () => {
            mesVista--;
            if (mesVista < 0) { mesVista = 11; anyoVista--; }
            renderizarCalendario();
        });
        btnNext.addEventListener('click', () => {
            mesVista++;
            if (mesVista > 11) { mesVista = 0; anyoVista++; }
            renderizarCalendario();
        });
        selectModalidad?.addEventListener('change', () => {
            if (!slotsModal.hidden) cargarDiasYRenderizar();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !slotsModal.hidden) cerrar();
        });
    })();
</script>
@endpush
