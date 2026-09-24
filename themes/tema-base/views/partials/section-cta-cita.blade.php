@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $telefonoLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
    $citaUrl = ($features['reservas'] ?? true)
        ? ($isLanding ? '#cita' : url('/pide-cita'))
        : ($telefonoLimpio ? 'tel:' . $telefonoLimpio : '#');
@endphp

<div class="layout__appointment">
    <div class="appointment__container">
        <h2 class="appointment__title">Si te apetece, escríbeme y empezamos cuanto antes.</h2>
        <a class="appointment__btn-appointment" href="{{ $citaUrl }}">Pide cita ahora</a>
    </div>
</div>
