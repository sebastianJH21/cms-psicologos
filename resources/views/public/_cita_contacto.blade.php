@php
    $telLimpioCO = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
    $telWaCO = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waUrlCO = !empty($social['whatsapp'])
        ? $social['whatsapp']
        : ($telWaCO ? 'https://wa.me/' . $telWaCO : null);
    $mapaQueryCO = $profile?->lat && $profile?->lng
        ? $profile->lat . ',' . $profile->lng
        : urlencode($profile?->direccion ?? '');
@endphp
<section class="cita-off" id="cita">
    <div class="cita-off__container">
        <header class="cita-off__header">
            <p class="cita-off__overline">{{ phrase('cita_overline', 'Pide cita') }}</p>
            <h2 class="cita-off__title">{{ phrase('cita_title', 'Reserva tu sesión') }}</h2>
            <p class="cita-off__lead">{{ phrase('cita_description', 'Elige el día y la hora que mejor te convenga.') }}</p>
        </header>

        <div class="cita-off__grid">
            <aside class="cita-off__map-col">
                @if ($profile?->direccion)
                    <h3 class="cita-off__subtitle"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> ¿Dónde estoy?</h3>
                    <p class="cita-off__address">{{ $profile->direccion }}</p>
                    <iframe
                        class="cita-off__iframe"
                        src="https://maps.google.com/maps?q={{ $mapaQueryCO }}&z=15&output=embed"
                        height="240"
                        title="Mapa de la consulta"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                @else
                    <h3 class="cita-off__subtitle"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Pide tu cita</h3>
                    <p class="cita-off__address">Escríbeme o llámame y concertamos una cita sin compromiso.</p>
                @endif
            </aside>

            <div class="cita-off__contact-col">
                <h3 class="cita-off__subtitle"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Contacto directo</h3>
                <p class="cita-off__contact-text">Contáctame por el medio que prefieras y te respondo lo antes posible.</p>
                <div class="cita-off__links">
                    @if ($profile?->telefono_publico)
                        <a href="tel:{{ $telLimpioCO }}" class="cita-off__link">
                            <i class="fa-solid fa-phone" aria-hidden="true"></i>
                            <span>{{ $profile->telefono_publico }}</span>
                        </a>
                    @endif
                    @if ($profile?->email_publico)
                        <a href="mailto:{{ $profile->email_publico }}" class="cita-off__link">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                            <span>{{ $profile->email_publico }}</span>
                        </a>
                    @endif
                    @if ($waUrlCO)
                        <a href="{{ $waUrlCO }}" target="_blank" rel="noopener" class="cita-off__link cita-off__link--wa">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                            <span>WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.cita-off {
    padding: 7rem 2rem;
    color: inherit;
}
.cita-off__container {
    max-width: 110rem;
    margin: 0 auto;
}
.cita-off__header {
    text-align: center;
    max-width: 64rem;
    margin: 0 auto 4.5rem;
}
.cita-off__overline {
    text-transform: uppercase;
    letter-spacing: 0.25rem;
    font-size: 1.3rem;
    font-weight: 700;
    opacity: 0.65;
    margin: 0 0 1rem;
}
.cita-off__title {
    font-size: clamp(2.8rem, 5vw, 4rem);
    line-height: 1.15;
    margin: 0 0 1.2rem;
}
.cita-off__lead {
    font-size: 1.6rem;
    line-height: 1.6;
    opacity: 0.8;
    margin: 0;
}
.cita-off__grid {
    display: grid;
    grid-template-columns: 0.85fr 1.15fr;
    gap: 2.4rem;
    align-items: start;
}
.cita-off__map-col,
.cita-off__contact-col {
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 1.4rem;
    padding: 2.6rem;
    background: rgba(0, 0, 0, 0.015);
}
.cita-off__contact-col {
    background: rgba(0, 0, 0, 0.04);
}
.cita-off__subtitle {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1.9rem;
    margin: 0 0 1.4rem;
}
.cita-off__address,
.cita-off__contact-text {
    font-size: 1.5rem;
    line-height: 1.6;
    opacity: 0.8;
    margin: 0 0 1.8rem;
}
.cita-off__iframe {
    width: 100%;
    border: 0;
    border-radius: 1rem;
    display: block;
}
.cita-off__links {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}
.cita-off__link {
    display: flex;
    align-items: center;
    gap: 1.1rem;
    padding: 1.4rem 1.7rem;
    border-radius: 1rem;
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.14);
    font-size: 1.55rem;
    font-weight: 600;
    color: inherit;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}
.cita-off__link i {
    width: 2.2rem;
    text-align: center;
    font-size: 1.7rem;
    opacity: 0.85;
}
.cita-off__link:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.8rem 2rem rgba(0, 0, 0, 0.1);
}
.cita-off__link--wa i {
    color: #25d366;
    opacity: 1;
}
@media (max-width: 860px) {
    .cita-off__grid {
        grid-template-columns: 1fr;
    }
    .cita-off__contact-col {
        order: -1;
    }
}
</style>
