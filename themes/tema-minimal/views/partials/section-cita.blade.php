@if ($features['reservas'] ?? true)
@php
    $modoVacaciones = $modoVacaciones ?? (bool) \App\Models\Setting::get('disponibilidad.modo_vacaciones', false);
    $mensajeVacaciones = $mensajeVacaciones ?? (string) \App\Models\Setting::get('disponibilidad.mensaje_vacaciones', '');
    $tieneOnline = $tieneOnline ?? \App\Models\Disponibilidad::where('modalidad','online')->where('activa',true)->exists();
    $tienePresencial = $tienePresencial ?? \App\Models\Disponibilidad::where('modalidad','presencial')->where('activa',true)->exists();
    $duracionSesion = $duracionSesion ?? (int) \App\Models\Setting::get('disponibilidad.duracion_sesion_min', 60);
    $telefonoLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
@endphp

<section class="t-min__cita-section" id="cita">
    <div class="t-min__container">
        <p class="t-min__overline">— {{ phrase('cita_overline', 'Reserva') }}</p>
        <h2 class="t-min__h2">{{ phrase('cita_title', 'Pide tu cita') }}</h2>

        @if ($modoVacaciones)
            <div class="t-min__cita-panel"><h3>Estoy de vacaciones</h3><p>{{ $mensajeVacaciones }}</p></div>
        @elseif (!$tieneOnline && !$tienePresencial)
            <div class="t-min__cita-panel"><h3>Sin disponibilidad</h3><p>Contáctame directamente.</p></div>
        @else
            <div class="t-min__cita-grid">
                <form id="reserva-form" class="cita-form" method="POST" action="{{ url('/reservas/crear') }}" novalidate>
                    @csrf
                    <input type="text" name="website" class="cita-form__honeypot" tabindex="-1" autocomplete="off">

                    <div class="cita-form__step" data-step="1">
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">1</span> Modalidad</h3>
                        <div class="cita-form__modalidades">
                            @if ($tienePresencial)
                                <label class="cita-form__modalidad"><input type="radio" name="modalidad" value="presencial" required>
                                    <div class="cita-form__modalidad-card"><i class="fa-solid fa-house-chimney"></i><strong>Presencial</strong><span>En consulta</span></div>
                                </label>
                            @endif
                            @if ($tieneOnline)
                                <label class="cita-form__modalidad"><input type="radio" name="modalidad" value="online" required>
                                    <div class="cita-form__modalidad-card"><i class="fa-solid fa-video"></i><strong>Online</strong><span>Por videollamada</span></div>
                                </label>
                            @endif
                        </div>
                    </div>
                    <div class="cita-form__step" data-step="2" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">2</span> Día</h3>
                        <div class="cita-calendar" id="cita-calendar">
                            <div class="cita-calendar__header">
                                <button type="button" class="cita-calendar__nav" data-action="prev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
                                <strong class="cita-calendar__month" id="cita-calendar-month"></strong>
                                <button type="button" class="cita-calendar__nav" data-action="next" aria-label="Siguiente"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                            <div class="cita-calendar__weekdays"><span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span></div>
                            <div class="cita-calendar__grid" id="cita-calendar-grid"></div>
                        </div>
                    </div>
                    <div class="cita-form__step" data-step="3" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">3</span> Hora</h3>
                        <p class="cita-form__step-hint" id="cita-slots-fecha"></p>
                        <div class="cita-form__slots" id="cita-slots"></div>
                        <input type="hidden" name="fecha_hora" id="cita-fecha-hora" required>
                    </div>
                    <div class="cita-form__step" data-step="4" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">4</span> Datos</h3>
                        <div class="cita-form__field"><label for="cita-nombre">Nombre completo *</label><input id="cita-nombre" type="text" name="nombre" maxlength="120" required></div>
                        <div class="cita-form__field"><label for="cita-telefono">Teléfono *</label><input id="cita-telefono" type="tel" name="telefono" maxlength="30" required></div>
                        <div class="cita-form__field"><label for="cita-motivo">Motivo</label><textarea id="cita-motivo" name="motivo" rows="4" maxlength="950"></textarea></div>
                        <div class="cita-form__resumen" id="cita-resumen"></div>
                        @include('public._reserva-seguridad')
                        <button type="submit" class="cita-form__btn-submit"><span class="cita-form__btn-text">Confirmar cita</span><span class="cita-form__btn-loading" hidden><i class="fa-solid fa-spinner fa-spin"></i> Enviando…</span></button>
                        <div class="cita-form__error" id="cita-form-error" hidden></div>
                    </div>
                </form>
                <aside class="t-min__cita-aside">
                    @if ($profile?->direccion)
                        <div class="t-min__cita-card">
                            <h3>Ubicación</h3>
                            <p>{{ $profile->direccion }}</p>
                            <iframe src="https://maps.google.com/maps?q={{ urlencode($profile->direccion) }}&output=embed" width="100%" height="200" style="border:0;" loading="lazy"></iframe>
                        </div>
                    @endif
                    <div class="t-min__cita-card">
                        <h3>Contacto directo</h3>
                        @if ($profile?->telefono_publico)<a href="tel:{{ $telefonoLimpio }}">{{ $profile->telefono_publico }}</a>@endif
                        @if ($profile?->email_publico)<a href="mailto:{{ $profile->email_publico }}">{{ $profile->email_publico }}</a>@endif
                        @php
                            $waMinimal = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telefonoLimpio ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $telefonoLimpio) : null);
                        @endphp
                        @if ($waMinimal)<a href="{{ $waMinimal }}" target="_blank" rel="noopener">WhatsApp →</a>@endif
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>

<div class="cita-modal" id="cita-modal" hidden>
    <div class="cita-modal__backdrop" data-cita-close></div>
    <div class="cita-modal__dialog" role="dialog" aria-modal="true">
        <button type="button" class="cita-modal__close" data-cita-close><i class="fa-solid fa-xmark"></i></button>
        <div class="cita-modal__body">
            <div class="cita-modal__icon"><i class="fa-solid fa-circle-check"></i></div>
            <h3>¡Cita reservada!</h3>
            <p id="cita-modal-text"></p>
            <div class="cita-modal__actions">
                <a id="cita-modal-gcal" href="#" target="_blank" rel="noopener" class="cita-modal__btn cita-modal__btn--primary"><i class="fa-brands fa-google"></i> Añadir a Google Calendar</a>
                <button type="button" class="cita-modal__btn cita-modal__btn--ghost" data-cita-close>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ theme_asset('assets/css/reserva.css', 'tema-base') }}">
<script>
window.PSICOCMS_RESERVA = {
    diasUrl: "{{ url('/reservas/dias') }}",
    slotsUrl: "{{ url('/reservas/slots') }}",
    crearUrl: "{{ url('/reservas/crear') }}",
    csrf: "{{ csrf_token() }}",
    duracion: {{ $duracionSesion }},
    psicologa: @json(trim(($user?->nombre ?? '') . ' ' . ($user?->apellidos ?? ''))),
};
</script>
<script src="{{ theme_asset('assets/js/reserva.js', 'tema-base') }}" defer></script>
@else
@include('public._cita_contacto')
@endif
