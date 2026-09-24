@php
    $reservasOnMF = $features['reservas'] ?? true;
    $telModFooterLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
    $citaUrl = $reservasOnMF
        ? (($themeMode ?? 'landing') === 'landing' ? url('/') . '#cita' : url('/pide-cita'))
        : ($telModFooterLimpio ? 'tel:' . $telModFooterLimpio : (($themeMode ?? 'landing') === 'landing' ? url('/') . '#cita' : url('/pide-cita')));
    $telModFooter = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waModFooter = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telModFooter ? 'https://wa.me/' . $telModFooter : null);
    $modFooterLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null];
@endphp
<footer class="t-mod__footer">
    <div class="t-mod__footer-cta">
        <h2>¿Hablamos?</h2>
        <p>{{ $reservasOnMF ? 'Reserva una primera sesión y vemos cómo puedo ayudarte.' : 'Llámame y concertamos una primera sesión sin compromiso.' }}</p>
        <a href="{{ $citaUrl }}" class="t-mod__btn">Pide cita ahora <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="t-mod__footer-grid">
        <div>
            <div class="t-mod__footer-brand">
                <span class="t-mod__footer-brand-mark">
                    @if (!empty($modFooterLogo['url']))<img src="{{ $modFooterLogo['url'] }}" alt="Logo">
                    @elseif (!empty($modFooterLogo['icon']))<i class="fa-solid {{ $modFooterLogo['icon'] }}"></i>
                    @else<i class="fa-solid fa-leaf"></i>@endif
                </span>
                <h4>{{ $user?->nombre }} {{ $user?->apellidos }}</h4>
            </div>
            <p>{{ $profile?->slogan }}</p>
            @if ($profile?->numero_colegiado)
                <p class="t-mod__footer-meta">Colegiada nº {{ $profile->numero_colegiado }}</p>
            @endif
        </div>
        <div>
            <h4>Contacto</h4>
            @if ($profile?->telefono_publico)
                <p><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a></p>
                @if ($waModFooter)
                    <p><a href="{{ $waModFooter }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>
                @endif
            @endif
            @if ($profile?->email_publico)<p><a href="mailto:{{ $profile->email_publico }}">{{ $profile->email_publico }}</a></p>@endif
            @if ($profile?->direccion)<p>{{ $profile->direccion }}</p>@endif
        </div>
        @if (!empty($social) && count(array_filter($social ?? [])) > 0)
        <div>
            <h4>Sígueme</h4>
            <div class="t-mod__social">
                @php
                    $icons = [
                        'instagram' => 'fa-brands fa-instagram',
                        'facebook' => 'fa-brands fa-facebook',
                        'linkedin' => 'fa-brands fa-linkedin',
                        'twitter' => 'fa-brands fa-twitter',
                        'youtube' => 'fa-brands fa-youtube',
                        'tiktok' => 'fa-brands fa-tiktok',
                        'whatsapp' => 'fa-brands fa-whatsapp',
                    ];
                @endphp
                @foreach ($icons as $key => $icon)
                    @if (!empty($social[$key]))
                        <a href="{{ $social[$key] }}" target="_blank" rel="noopener"><i class="{{ $icon }}"></i></a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="t-mod__footer-bottom">
        <p>© {{ date('Y') }} {{ $user?->nombre }} {{ $user?->apellidos }} · <a href="https://victorroblesweb.es" target="_blank" rel="noopener" class="t-mod__credit-link">Todos los derechos reservados</a> · <a href="{{ route('public.privacidad') }}" class="t-mod__credit-link">Política de privacidad</a></p>
    </div>
</footer>
