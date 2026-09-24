@if ($features['reservas'] ?? true)
@php
    $modoVacaciones = $modoVacaciones ?? (bool) \App\Models\Setting::get('disponibilidad.modo_vacaciones', false);
    $mensajeVacaciones = $mensajeVacaciones ?? (string) \App\Models\Setting::get('disponibilidad.mensaje_vacaciones', '');
    $tieneOnline = $tieneOnline ?? \App\Models\Disponibilidad::where('modalidad','online')->where('activa',true)->exists();
    $tienePresencial = $tienePresencial ?? \App\Models\Disponibilidad::where('modalidad','presencial')->where('activa',true)->exists();
    $duracionSesion = $duracionSesion ?? (int) \App\Models\Setting::get('disponibilidad.duracion_sesion_min', 60);
    $telefonoLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
@endphp

<section class="layout__cita-section" id="cita">
    <div class="cita-section__container">
        <header class="cita-section__header">
            <h4 class="cita-section__overtitle">{{ phrase('cita_overline', 'Reserva online') }}</h4>
            <h2 class="cita-section__title">{{ phrase('cita_title', 'Pide tu cita') }}</h2>
            <p class="cita-section__lead">{{ phrase('cita_description', 'Elige la modalidad, día y hora que mejor te venga. Te confirmaré por teléfono.') }}</p>
        </header>

        @if ($modoVacaciones)
            <div class="cita-section__panel cita-section__panel--vacaciones">
                <i class="fa-solid fa-umbrella-beach cita-section__big-ico"></i>
                <h3>Estoy de vacaciones</h3>
                <p>{{ $mensajeVacaciones ?: 'Estoy fuera unos días. Vuelvo pronto. Gracias por tu paciencia.' }}</p>
            </div>
        @elseif (!$tieneOnline && !$tienePresencial)
            <div class="cita-section__panel cita-section__panel--info">
                <i class="fa-solid fa-clock cita-section__big-ico"></i>
                <h3>Aún no hay disponibilidad publicada</h3>
                <p>Contáctame directamente y agendamos sin problema.</p>
                <div class="cita-section__contactos">
                    @if ($profile?->telefono_publico)
                        <a href="tel:{{ $telefonoLimpio }}" class="cita-section__btn-contacto"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a>
                    @endif
                    @php
                        $waBaseHero = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telefonoLimpio ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $telefonoLimpio) : null);
                    @endphp
                    @if ($waBaseHero)
                        <a href="{{ $waBaseHero }}" target="_blank" rel="noopener" class="cita-section__btn-contacto cita-section__btn-contacto--wa"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                    @endif
                </div>
            </div>
        @else
            <div class="cita-section__grid">
                <form id="reserva-form" class="cita-form" method="POST" action="{{ url('/reservas/crear') }}" novalidate>
                    @csrf
                    <input type="text" name="website" class="cita-form__honeypot" tabindex="-1" autocomplete="off">

                    <div class="cita-form__step" data-step="1">
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">1</span> Modalidad</h3>
                        <div class="cita-form__modalidades">
                            @if ($tienePresencial)
                                <label class="cita-form__modalidad">
                                    <input type="radio" name="modalidad" value="presencial" required>
                                    <div class="cita-form__modalidad-card">
                                        <i class="fa-solid fa-house-chimney"></i>
                                        <strong>Presencial</strong>
                                        <span>En consulta</span>
                                    </div>
                                </label>
                            @endif
                            @if ($tieneOnline)
                                <label class="cita-form__modalidad">
                                    <input type="radio" name="modalidad" value="online" required>
                                    <div class="cita-form__modalidad-card">
                                        <i class="fa-solid fa-video"></i>
                                        <strong>Online</strong>
                                        <span>Por videollamada</span>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="cita-form__step" data-step="2" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">2</span> Elige día</h3>
                        <div class="cita-calendar" id="cita-calendar">
                            <div class="cita-calendar__header">
                                <button type="button" class="cita-calendar__nav" data-action="prev" aria-label="Mes anterior"><i class="fa-solid fa-chevron-left"></i></button>
                                <strong class="cita-calendar__month" id="cita-calendar-month"></strong>
                                <button type="button" class="cita-calendar__nav" data-action="next" aria-label="Mes siguiente"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                            <div class="cita-calendar__weekdays">
                                <span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span>
                            </div>
                            <div class="cita-calendar__grid" id="cita-calendar-grid"></div>
                        </div>
                    </div>

                    <div class="cita-form__step" data-step="3" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">3</span> Elige hora</h3>
                        <p class="cita-form__step-hint" id="cita-slots-fecha"></p>
                        <div class="cita-form__slots" id="cita-slots"></div>
                        <input type="hidden" name="fecha_hora" id="cita-fecha-hora" required>
                    </div>

                    <div class="cita-form__step" data-step="4" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">4</span> Tus datos</h3>

                        <div class="cita-form__field">
                            <label for="cita-nombre">Nombre completo *</label>
                            <input id="cita-nombre" type="text" name="nombre" maxlength="120" required>
                        </div>
                        <div class="cita-form__field">
                            <label for="cita-telefono">Teléfono *</label>
                            <input id="cita-telefono" type="tel" name="telefono" maxlength="30" required>
                        </div>
                        <div class="cita-form__field">
                            <label for="cita-motivo">Motivo de la consulta</label>
                            <textarea id="cita-motivo" name="motivo" rows="4" maxlength="950"></textarea>
                        </div>

                        <div class="cita-form__resumen" id="cita-resumen"></div>

                        @include('public._reserva-seguridad')

                        <button type="submit" class="cita-form__btn-submit">
                            <span class="cita-form__btn-text">Confirmar cita</span>
                            <span class="cita-form__btn-loading" hidden><i class="fa-solid fa-spinner fa-spin"></i> Enviando…</span>
                        </button>
                        <div class="cita-form__error" id="cita-form-error" hidden></div>
                    </div>
                </form>

                <aside class="cita-section__aside">
                    @if ($profile?->direccion)
                        <div class="cita-section__card">
                            <h3><i class="fa-solid fa-location-dot"></i> ¿Dónde estoy?</h3>
                            <p>{{ $profile->direccion }}</p>
                            @if ($profile->lat && $profile->lng)
                                <iframe src="https://maps.google.com/maps?q={{ $profile->lat }},{{ $profile->lng }}&z=15&output=embed" width="100%" height="220" style="border:0; border-radius:1rem;" allowfullscreen="" loading="lazy"></iframe>
                            @else
                                <iframe src="https://maps.google.com/maps?q={{ urlencode($profile->direccion) }}&output=embed" width="100%" height="220" style="border:0; border-radius:1rem;" allowfullscreen="" loading="lazy"></iframe>
                            @endif
                        </div>
                    @endif

                    <div class="cita-section__card">
                        <h3><i class="fa-solid fa-circle-info"></i> Contacto directo</h3>
                        @if ($profile?->telefono_publico)
                            <a href="tel:{{ $telefonoLimpio }}" class="cita-section__contact-link"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a>
                        @endif
                        @if ($profile?->email_publico)
                            <a href="mailto:{{ $profile->email_publico }}" class="cita-section__contact-link"><i class="fa-solid fa-envelope"></i> {{ $profile->email_publico }}</a>
                        @endif
                        @php
                            $waBaseAside = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telefonoLimpio ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $telefonoLimpio) : null);
                        @endphp
                        @if ($waBaseAside)
                            <a href="{{ $waBaseAside }}" target="_blank" rel="noopener" class="cita-section__contact-link cita-section__contact-link--wa"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                        @endif
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>

<div class="cita-modal" id="cita-modal" hidden>
    <div class="cita-modal__backdrop" data-cita-close></div>
    <div class="cita-modal__dialog" role="dialog" aria-modal="true">
        <button type="button" class="cita-modal__close" data-cita-close aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
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
