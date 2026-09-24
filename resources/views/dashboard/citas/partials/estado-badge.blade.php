@php
    $pair = $pair ?? ['pendiente', 'confirmada'];
    $estadoClase = match ($cita->estado) {
        'confirmada' => 'success',
        'realizada'  => 'info',
        'cancelada'  => 'danger',
        'no_asistio' => 'warning',
        default      => 'info',
    };
    $puedeToggle = in_array($cita->estado, $pair, true);
@endphp

@if ($puedeToggle)
    <button type="button"
        class="badge badge--{{ $estadoClase }} cita-estado-toggle"
        data-url="{{ route('dashboard.citas.estado', $cita) }}"
        data-estado="{{ $cita->estado }}"
        data-pair="{{ implode(',', $pair) }}"
        title="Cambiar estado de la cita">
        <span class="cita-estado-toggle__label">{{ $cita->estado_label }}</span>
        <i class="fa-solid fa-arrows-rotate cita-estado-toggle__ico" aria-hidden="true"></i>
    </button>
@else
    <span class="badge badge--{{ $estadoClase }}">{{ $cita->estado_label }}</span>
@endif
