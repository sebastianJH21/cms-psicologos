@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $telFooterLimpio = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waFooter = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telFooterLimpio ? 'https://wa.me/' . $telFooterLimpio : null);
@endphp

<footer class="t-sage__footer">
    <div class="t-sage__footer-grid">
        <div class="t-sage__footer-col">
            <div class="t-sage__brand">
                <div class="t-sage__brand-mark">
                @php $footerLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
                @if (!empty($footerLogo['url']))<img src="{{ $footerLogo['url'] }}" alt="Logo">
                @elseif (!empty($footerLogo['icon']))<i class="fa-solid {{ $footerLogo['icon'] }}"></i>
                @else<i class="fa-solid fa-leaf"></i>@endif
            </div>
                <div class="t-sage__brand-text">
                    <strong>{{ $user?->nombre }} {{ $user?->apellidos }}</strong>
                    <span>{{ $profile?->slogan ?? 'Psicología clínica' }}</span>
                </div>
            </div>
            @if ($profile?->numero_colegiado)
                <p class="t-sage__footer-text"><strong>N.º colegiado:</strong> {{ $profile->numero_colegiado }}</p>
            @endif
        </div>

        <div class="t-sage__footer-col">
            <h4>Contacto</h4>
            @if ($profile?->telefono_publico)
                <p><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a></p>
                @if ($waFooter)
                    <p><a href="{{ $waFooter }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>
                @endif
            @endif
            @if ($profile?->email_publico)
                <p><a href="mailto:{{ $profile->email_publico }}"><i class="fa-solid fa-envelope"></i> {{ $profile->email_publico }}</a></p>
            @endif
            @if ($profile?->direccion)
                <p><i class="fa-solid fa-location-dot"></i> {{ $profile->direccion }}</p>
            @endif
        </div>

        <div class="t-sage__footer-col">
            @if (!empty($social) && count(array_filter($social ?? [])) > 0)
                <h4>Sígueme</h4>
                <div class="t-sage__social">
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
                            <a href="{{ $social[$key] }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($key) }}"><i class="{{ $icon }}"></i></a>
                        @endif
                    @endforeach
                </div>
            @endif
            @if ($features['reservas'] ?? true)
                <a href="{{ $citaUrl }}" class="t-sage__footer-cta">Solicitar cita</a>
            @endif
        </div>
    </div>

    <div class="t-sage__footer-bottom">
        <p>© {{ date('Y') }} {{ $user?->nombre }} {{ $user?->apellidos }}. <a href="https://victorroblesweb.es" target="_blank" rel="noopener" class="t-sage__credit-link">Todos los derechos reservados.</a> · <a href="{{ route('public.privacidad') }}" class="t-sage__credit-link">Política de privacidad</a></p>
    </div>
</footer>
