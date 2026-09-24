@php
    $telefonoLimpioFooter = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $whatsappLink = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telefonoLimpioFooter ? 'https://wa.me/' . $telefonoLimpioFooter : null);
    $footerLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null];
@endphp
<footer class="t-warm__footer">
    <svg class="t-warm__footer-wave" viewBox="0 0 1200 100" preserveAspectRatio="none">
        <path d="M0,40 C300,90 600,0 1200,50 L1200,100 L0,100 Z" fill="currentColor"></path>
    </svg>
    <div class="t-warm__footer-content">
        <div class="t-warm__footer-grid">
            <div>
                <div class="t-warm__footer-brand">
                    <div class="t-warm__footer-brand-mark">
                        @if (!empty($footerLogo['url']))<img src="{{ $footerLogo['url'] }}" alt="Logo">
                        @elseif (!empty($footerLogo['icon']))<i class="fa-solid {{ $footerLogo['icon'] }}"></i>
                        @else<span class="t-warm__brand-flower">🌸</span>@endif
                    </div>
                    <h4>{{ $user?->nombre }} {{ $user?->apellidos }}</h4>
                </div>
                <p>{{ $profile?->slogan }}</p>
                @if ($profile?->numero_colegiado)
                    <p class="t-warm__footer-meta">Colegiada nº {{ $profile->numero_colegiado }}</p>
                @endif
            </div>
            <div>
                <h4>Contacto</h4>
                @if ($profile?->telefono_publico)
                    <p><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a></p>
                    @if ($whatsappLink)
                        <p><a href="{{ $whatsappLink }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>
                    @endif
                @endif
                @if ($profile?->email_publico)<p><a href="mailto:{{ $profile->email_publico }}"><i class="fa-solid fa-envelope"></i> {{ $profile->email_publico }}</a></p>@endif
                @if ($profile?->direccion)<p><i class="fa-solid fa-location-dot"></i> {{ $profile->direccion }}</p>@endif
            </div>
            @if (!empty($social) && count(array_filter($social ?? [])) > 0)
            <div>
                <h4>Sígueme</h4>
                <div class="t-warm__social">
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
        <div class="t-warm__footer-bottom">
            <p>Hecho con <i class="fa-solid fa-heart"></i> · © {{ date('Y') }} {{ $user?->nombre }} {{ $user?->apellidos }} · <a href="https://victorroblesweb.es" target="_blank" rel="noopener" class="t-warm__credit-link">Todos los derechos reservados.</a> · <a href="{{ route('public.privacidad') }}" class="t-warm__credit-link">Política de privacidad</a></p>
        </div>
    </div>
</footer>
