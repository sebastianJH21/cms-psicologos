@extends('dashboard.layout')

@section('titulo', 'Detalle de cita')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Citas', 'url' => route('dashboard.citas.index')],
        ['label' => 'Cita #' . $cita->id],
    ];
    $estadoClase = match ($cita->estado) {
        'confirmada' => 'success',
        'realizada' => 'info',
        'cancelada' => 'danger',
        'no_asistio' => 'warning',
        default => 'info',
    };
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/citas.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Cita #{{ $cita->id }}</h1>
            <p class="page-header__subtitle">{{ $cita->fecha_inicio->translatedFormat('l d \\d\\e F \\d\\e Y, H:i') }}</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.citas.edit', $cita) }}" class="btn btn--primary">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                Editar
            </a>
            @if ($cita->estado !== 'cancelada')
                <form method="POST" action="{{ route('dashboard.citas.destroy', $cita) }}"
                    data-confirm="¿Cancelar esta cita?"
                    data-confirm-title="Cancelar cita"
                    data-confirm-label="Sí, cancelar"
                    data-confirm-style="danger">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--danger">
                        <i class="fa-solid fa-ban" aria-hidden="true"></i>
                        Cancelar cita
                    </button>
                </form>
            @endif
        </div>
    </header>

    <div class="cita-show">
        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Datos de la cita</h2>
                @include('dashboard.citas.partials.estado-badge', ['cita' => $cita, 'pair' => ['pendiente', 'confirmada']])
            </header>

            <dl class="cita-show__list">
                <div>
                    <dt><i class="fa-solid fa-video" aria-hidden="true"></i> Modalidad</dt>
                    <dd>
                        <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">
                            {{ $cita->modalidad_label }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-clock" aria-hidden="true"></i> Inicio</dt>
                    <dd>{{ $cita->fecha_inicio->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-clock" aria-hidden="true"></i> Fin</dt>
                    <dd>{{ $cita->fecha_fin->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-tag" aria-hidden="true"></i> Origen</dt>
                    <dd>{{ $cita->origen === 'publica' ? 'Reserva pública' : 'Manual' }}</dd>
                </div>
            </dl>
        </section>

        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title"><i class="fa-solid fa-user" aria-hidden="true"></i> Paciente</h2>
            </header>

            @php
                $citaTelefono = $cita->paciente_telefono;
                $citaEmail = $cita->paciente?->email ?: $cita->email_provisional;
            @endphp
            <dl class="cita-show__list">
                <div>
                    <dt><i class="fa-solid fa-id-card" aria-hidden="true"></i> Nombre</dt>
                    <dd>
                        @if ($cita->paciente_id)
                            <a href="{{ route('dashboard.pacientes.show', $cita->paciente) }}" class="cita-show__link">
                                {{ $cita->paciente_nombre }}
                            </a>
                        @else
                            {{ $cita->paciente_nombre }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-phone" aria-hidden="true"></i> Teléfono</dt>
                    <dd>
                        @if ($citaTelefono)
                            <a href="tel:{{ preg_replace('/[^+0-9]/', '', $citaTelefono) }}" class="cita-show__link">
                                {{ $citaTelefono }}
                            </a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                @php $waCita = whatsapp_confirmacion_url($cita); @endphp
                @if ($waCita)
                    <div>
                        <dt><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</dt>
                        <dd>
                            <a href="{{ $waCita }}" target="_blank" rel="noopener" class="cita-show__link cita-show__link--wa">
                                Confirmar cita por WhatsApp
                            </a>
                        </dd>
                    </div>
                @endif
                @if ($citaEmail)
                    <div>
                        <dt><i class="fa-solid fa-envelope" aria-hidden="true"></i> Email</dt>
                        <dd>
                            <a href="mailto:{{ $citaEmail }}" class="cita-show__link">
                                {{ $citaEmail }}
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>

            @if (!$cita->paciente_id)
                <p class="cita-show__hint">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    Esta cita aún no está vinculada a una ficha de paciente. La vinculación automática se habilitará en una próxima fase.
                </p>
            @endif
        </section>

        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title"><i class="fa-solid fa-message" aria-hidden="true"></i> Motivo de la consulta</h2>
            </header>
            <p class="cita-show__text">
                {{ $cita->motivo ?: 'Sin motivo indicado.' }}
            </p>
        </section>

        @if ($cita->notas_internas)
            <section class="panel">
                <header class="panel__header">
                    <h2 class="panel__title"><i class="fa-solid fa-lock" aria-hidden="true"></i> Notas internas</h2>
                </header>
                <p class="cita-show__text cita-show__text--muted">{{ $cita->notas_internas }}</p>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/cita-estado.js') }}" defer></script>
@endpush
