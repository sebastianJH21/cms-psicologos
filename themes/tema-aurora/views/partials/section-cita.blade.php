@php
    $modoVacaciones = $modoVacaciones ?? (bool) \App\Models\Setting::get('disponibilidad.modo_vacaciones', false);
    $mensajeVacaciones = $mensajeVacaciones ?? (string) \App\Models\Setting::get('disponibilidad.mensaje_vacaciones', '');
    $tieneOnline = $tieneOnline ?? \App\Models\Disponibilidad::where('modalidad','online')->where('activa',true)->exists();
    $tienePresencial = $tienePresencial ?? \App\Models\Disponibilidad::where('modalidad','presencial')->where('activa',true)->exists();
    $duracionSesion = $duracionSesion ?? (int) \App\Models\Setting::get('disponibilidad.duracion_sesion_min', 60);
    $telefonoLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
    $telLimpioWa = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waUrl = $social['whatsapp'] ?? ($telLimpioWa ? 'https://wa.me/' . $telLimpioWa : null);
@endphp
@if ($features['reservas'] ?? true)
<section class="t-aurora__cita" id="cita">
    <div class="t-aurora__container">
        <header class="t-aurora__sec-head">
            <span class="t-aurora__overline">{{ phrase('cita_overline', '— Reserva online') }}</span>
            <h2 class="t-aurora__h2">{{ phrase('cita_title', 'Empezamos juntas') }}</h2>
            @if (phrase('cita_description'))<p>{{ phrase('cita_description') }}</p>@endif
        </header>

        @if ($modoVacaciones)
            <div class="t-aurora__cita-panel">
                <i class="fa-solid fa-umbrella-beach"></i>
                <h3>Estoy de vacaciones</h3>
                <p>{{ $mensajeVacaciones ?: 'Volveré pronto, gracias por tu paciencia.' }}</p>
            </div>
        @elseif (!$tieneOnline && !$tienePresencial)
            <div class="t-aurora__cita-panel">
                <i class="fa-solid fa-clock"></i>
                <h3>Aún no hay disponibilidad publicada</h3>
                @if ($profile?->telefono_publico)<a href="tel:{{ $telefonoLimpio }}" class="t-aurora__btn t-aurora__btn--primary">Llamar al {{ $profile->telefono_publico }}</a>@endif
            </div>
        @else
            <div class="t-aurora__cita-grid">
                <form id="reserva-form" class="cita-form" method="POST" action="{{ url('/reservas/crear') }}" novalidate>
                    @csrf
                    <input type="hidden" @if(!($tieneOnline && $tienePresencial)) name="modalidad" @endif id="cita-modalidad" value="{{ ($tieneOnline && $tienePresencial) ? '' : ($tienePresencial ? 'presencial' : 'online') }}">
                    <input type="hidden" name="fecha_hora" id="cita-fecha-hora-input">
                    <input type="text" name="website" class="cita-form__honeypot" tabindex="-1" autocomplete="off">

                    @php $stepBase = ($tieneOnline && $tienePresencial) ? 2 : 1; @endphp

                    @if ($tieneOnline && $tienePresencial)
                        <div class="cita-form__step" data-step="1">
                            <h3 class="cita-form__step-title"><span class="cita-form__step-num">1</span> Modalidad</h3>
                            <div class="cita-form__modalidades">
                                <label class="cita-form__modalidad">
                                    <input type="radio" name="modalidad" value="presencial">
                                    <div class="cita-form__modalidad-card">
                                        <i class="fa-solid fa-house-medical"></i>
                                        <strong>Presencial</strong>
                                        <span>En consulta</span>
                                    </div>
                                </label>
                                <label class="cita-form__modalidad">
                                    <input type="radio" name="modalidad" value="online">
                                    <div class="cita-form__modalidad-card">
                                        <i class="fa-solid fa-video"></i>
                                        <strong>Online</strong>
                                        <span>Videollamada</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="cita-form__step" data-step="{{ $stepBase }}" @if($stepBase > 1) hidden @endif>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">{{ $stepBase }}</span> Elige día</h3>
                        <div class="cita-calendar" id="cita-calendar">
                            <div class="cita-calendar__header">
                                <button type="button" class="cita-calendar__nav" id="cita-cal-prev" aria-label="Mes anterior">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <strong class="cita-calendar__month" id="cita-calendar-month"></strong>
                                <button type="button" class="cita-calendar__nav" id="cita-cal-next" aria-label="Mes siguiente">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="cita-calendar__weekdays">
                                <span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span>
                            </div>
                            <div class="cita-calendar__grid" id="cita-calendar-grid"></div>
                        </div>
                    </div>

                    <div class="cita-form__step" data-step="{{ $stepBase + 1 }}" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">{{ $stepBase + 1 }}</span> Hora</h3>
                        <div class="cita-form__slots" id="cita-slots"></div>
                    </div>

                    <div class="cita-form__step" data-step="{{ $stepBase + 2 }}" hidden>
                        <h3 class="cita-form__step-title"><span class="cita-form__step-num">{{ $stepBase + 2 }}</span> Tus datos</h3>
                        <div class="cita-form__field"><label>Nombre completo *</label><input type="text" id="cita-nombre" name="nombre" required maxlength="120"></div>
                        <div class="cita-form__field"><label>Teléfono *</label><input type="tel" id="cita-telefono" name="telefono" required maxlength="30"></div>
                        <div class="cita-form__field"><label>Motivo (opcional)</label><textarea id="cita-motivo" name="motivo" rows="3" maxlength="800"></textarea></div>
                        @include('public._reserva-seguridad')
                        <button type="submit" class="t-aurora__btn t-aurora__btn--primary" id="cita-submit">Confirmar reserva</button>
                        <div class="cita-form__error" id="cita-form-error" hidden></div>
                    </div>
                </form>

                <aside class="t-aurora__cita-aside">
                    @if ($profile?->direccion)
                        <div class="t-aurora__cita-card">
                            <h3><i class="fa-solid fa-location-dot"></i> Dónde encontrarme</h3>
                            <p>{{ $profile->direccion }}</p>
                            <div class="t-aurora__cita-map">
                                <iframe
                                    src="https://www.google.com/maps?q={{ urlencode($profile->direccion) }}&output=embed"
                                    width="100%"
                                    height="220"
                                    style="border:0"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif
                    <div class="t-aurora__cita-card">
                        <h3><i class="fa-solid fa-circle-info"></i> Contacto directo</h3>
                        @if ($profile?->telefono_publico)<p><a href="tel:{{ $telefonoLimpio }}"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a></p>@endif
                        @if ($telLimpioWa)<p><a href="https://wa.me/{{ $telLimpioWa }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>@endif
                        @if ($profile?->email_publico)<p><a href="mailto:{{ $profile->email_publico }}"><i class="fa-solid fa-envelope"></i> {{ $profile->email_publico }}</a></p>@endif
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>

@push('scripts')
    <script src="{{ theme_asset('assets/js/cita-form.js') }}" defer></script>
@endpush
@else
@include('public._cita_contacto')
@endif
