@php
    $cita = $cita ?? null;
    $fechaInicioValue = old('fecha_inicio', optional($cita?->fecha_inicio)->format('Y-m-d\TH:i') ?? ($fechaPredeterminada ?? now()->addHour()->format('Y-m-d\TH:00')));
@endphp

<input type="hidden" name="paciente_id" id="field-paciente-id" value="{{ old('paciente_id', $cita?->paciente_id) }}">

@if ($cita === null)
<div class="form-field paciente-busqueda" id="paciente-busqueda">
    <label for="input-buscar-paciente">Buscar paciente existente</label>
    <div class="paciente-busqueda__wrap">
        <input type="text" id="input-buscar-paciente" autocomplete="off"
            placeholder="Nombre, teléfono o email..."
            data-search-url="{{ route('dashboard.pacientes.buscar') }}">
        <button type="button" class="paciente-busqueda__clear" id="btn-limpiar-paciente" hidden aria-label="Limpiar selección">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
        <div class="paciente-busqueda__dropdown" id="dropdown-pacientes" hidden role="listbox" aria-label="Resultados"></div>
    </div>
    <div class="paciente-busqueda__seleccionado" id="paciente-seleccionado" hidden></div>
    <small class="form-field__hint">Si el paciente no existe se creará automáticamente al guardar.</small>
</div>
@endif

{{-- Fila 1: nombre · teléfono · email --}}
@if ($cita === null)
<div class="cita-form__grid">
    <div class="form-field">
        <label for="nombre_provisional">Nombre del paciente *</label>
        <input type="text" id="nombre_provisional" name="nombre_provisional" maxlength="150" required value="{{ old('nombre_provisional', $cita?->nombre_provisional) }}">
        @error('nombre_provisional')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="telefono_provisional">Teléfono *</label>
        <input type="tel" id="telefono_provisional" name="telefono_provisional" maxlength="30" required value="{{ old('telefono_provisional', $cita?->telefono_provisional) }}">
        @error('telefono_provisional')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="email_provisional">Email (opcional)</label>
        <input type="email" id="email_provisional" name="email_provisional" maxlength="150" value="{{ old('email_provisional', $cita?->email_provisional) }}">
        @error('email_provisional')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>
@else
    @php
        $datoNombre = $cita->paciente_nombre;
        $datoTelefono = $cita->paciente_telefono;
        $datoEmail = $cita->paciente?->email ?: $cita->email_provisional;
        $datoTelLimpio = preg_replace('/[^+0-9]/', '', (string) $datoTelefono);
        $waCitaEdit = whatsapp_confirmacion_url($cita);
    @endphp

    <input type="hidden" name="nombre_provisional" value="{{ $cita->nombre_provisional }}">
    <input type="hidden" name="telefono_provisional" value="{{ $cita->telefono_provisional }}">
    <input type="hidden" name="email_provisional" value="{{ $cita->email_provisional }}">

    <div class="form-field">
        <label>Datos del paciente</label>
        <p class="form-field__hint">Estos datos solo se pueden modificar en la
            <a href="{{ $cita->paciente ? route('dashboard.pacientes.edit', $cita->paciente) : route('dashboard.pacientes.index') }}" class="cita-show__link">ficha del paciente</a>.
        </p>
        <div class="cita-datos-badges">
            @if ($cita->paciente)
                <a href="{{ route('dashboard.pacientes.show', $cita->paciente) }}" class="cita-dato-badge">
                    <i class="fa-solid fa-user" aria-hidden="true"></i> {{ $datoNombre }}
                </a>
            @else
                <span class="cita-dato-badge cita-dato-badge--plain">
                    <i class="fa-solid fa-user" aria-hidden="true"></i> {{ $datoNombre }}
                </span>
            @endif

            @if ($datoTelefono)
                <a href="tel:{{ $datoTelLimpio }}" class="cita-dato-badge">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i> {{ $datoTelefono }}
                </a>
            @endif

            @if ($datoEmail)
                <a href="mailto:{{ $datoEmail }}" class="cita-dato-badge">
                    <i class="fa-solid fa-envelope" aria-hidden="true"></i> {{ $datoEmail }}
                </a>
            @endif

            @if ($waCitaEdit)
                <a href="{{ $waCitaEdit }}" target="_blank" rel="noopener" class="cita-dato-badge cita-dato-badge--wa">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Confirmar cita por WhatsApp
                </a>
            @endif
        </div>
    </div>
@endif

{{-- Fila 2: modalidad · fecha · estado --}}
<div class="cita-form__grid">
    <div class="form-field">
        <label for="modalidad">Modalidad *</label>
        <select id="modalidad" name="modalidad" required>
            @foreach ($modalidades as $key => $label)
                <option value="{{ $key }}" {{ old('modalidad', $cita?->modalidad) === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('modalidad')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="fecha_inicio">Fecha y hora *</label>
        <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required readonly value="{{ $fechaInicioValue }}" class="form-input--locked">
        <small class="form-field__hint" id="hint-duracion-cita"
            data-duracion="{{ $duracion }}"
            data-descanso-presencial="{{ $descansoPresencial ?? 0 }}"
            data-descanso-online="{{ $descansoOnline ?? 0 }}">Duración: {{ $duracion }} min.</small>
        <script>
            (function () {
                const select = document.getElementById('modalidad');
                const hint   = document.getElementById('hint-duracion-cita');
                if (!select || !hint) return;
                const dur = parseInt(hint.dataset.duracion, 10);
                const descansos = {
                    presencial: parseInt(hint.dataset.descansoPresencial, 10),
                    online:     parseInt(hint.dataset.descansoOnline, 10),
                };
                const actualizar = () => {
                    const d = descansos[select.value] ?? 0;
                    hint.textContent = d > 0
                        ? `Duración: ${dur} min. + ${d} min. de descanso (huecos cada ${dur + d} min.)`
                        : `Duración: ${dur} min.`;
                };
                select.addEventListener('change', actualizar);
                actualizar();
            })();
        </script>
        <div class="cita-fecha-botones">
            <button type="button" class="btn btn--ghost btn--sm btn-huecos-anim" id="btn-disponibilidad-cita">
                <i class="fa-solid fa-calendar-check"></i> Ver huecos disponibles
            </button>
            <button type="button" class="btn btn--ghost btn--sm" id="btn-fecha-manual">
                <i class="fa-solid fa-pen"></i> Elegir fecha manualmente
            </button>
        </div>
        @error('fecha_inicio')<small class="form-field__error">{{ $message }}</small>@enderror
        @error('fecha_fin')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            @foreach ($estados as $key => $label)
                @if ($cita || $key !== 'cancelada')
                    <option value="{{ $key }}" {{ old('estado', $cita?->estado ?? 'confirmada') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endif
            @endforeach
        </select>
        @error('estado')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field">
    <label for="motivo">Motivo de la consulta</label>
    <textarea id="motivo" name="motivo" rows="3" maxlength="1000">{{ old('motivo', $cita?->motivo) }}</textarea>
    <small class="form-field__hint">Máximo 1000 caracteres.</small>
    @error('motivo')<small class="form-field__error">{{ $message }}</small>@enderror
</div>

<div class="form-field">
    <label for="notas_internas">Notas internas (privadas)</label>
    <textarea id="notas_internas" name="notas_internas" rows="5" maxlength="5000">{{ old('notas_internas', $cita?->notas_internas) }}</textarea>
    <small class="form-field__hint">Máximo 5000 caracteres. Solo tú las verás.</small>
    @error('notas_internas')<small class="form-field__error">{{ $message }}</small>@enderror
</div>

{{-- Modal de disponibilidad con calendario --}}
<div class="cita-slots-modal" id="cita-slots-modal" role="dialog" aria-modal="true" hidden>
    <div class="cita-slots-modal__backdrop" id="cita-slots-modal-backdrop"></div>
    <div class="cita-slots-modal__box">
        <header class="cita-slots-modal__header">
            <h3><i class="fa-solid fa-calendar-check"></i> Selecciona un hueco disponible</h3>
            <button type="button" class="btn btn--icon" id="cita-slots-close" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <div class="cita-slots-modal__body">
            <div class="cita-slots-modal__cal">
                <div class="cita-slots-modal__cal-head">
                    <button type="button" class="btn btn--icon" id="cita-slots-prev" aria-label="Mes anterior"><i class="fa-solid fa-chevron-left"></i></button>
                    <strong id="cita-slots-titulo">—</strong>
                    <button type="button" class="btn btn--icon" id="cita-slots-next" aria-label="Mes siguiente"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <div class="cita-slots-modal__cal-grid" id="cita-slots-grid"></div>
            </div>
            <div class="cita-slots-modal__slots">
                <p class="cita-slots-modal__slots-hint" id="cita-slots-hint">Selecciona un día con disponibilidad.</p>
                <div class="cita-slots-modal__slots-list" id="cita-slots-list"></div>
            </div>
        </div>
    </div>
</div>
