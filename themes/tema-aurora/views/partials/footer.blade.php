@php
    $telLimpioFooter = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waUrl = $social['whatsapp'] ?? ($telLimpioFooter ? 'https://wa.me/' . $telLimpioFooter : null);
    $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null];
@endphp
<footer class="t-aurora__footer">
    <div class="t-aurora__footer-inner">
        <div class="t-aurora__footer-brand">
            <div class="t-aurora__footer-logo">
                @if (!empty($brandLogo['url']))<img src="{{ $brandLogo['url'] }}" alt="Logo">
                @elseif (!empty($brandLogo['icon']))<i class="fa-solid {{ $brandLogo['icon'] }}"></i>
                @else<i class="fa-solid fa-feather-pointed"></i>@endif
            </div>
            <div>
                <h3>{{ $user?->nombre }} {{ $user?->apellidos }}</h3>
                <p>{{ $profile?->slogan ?? 'Psicología consciente' }}</p>
            </div>
        </div>

        <div class="t-aurora__footer-cols">
            <div>
                <h4>Navegación</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Inicio</a></li>
                    @if ($features['sobre_mi'] ?? true)<li><a href="{{ url('/sobre-mi') }}">Sobre mí</a></li>@endif
                    @if ($features['servicios'] ?? true)<li><a href="{{ url('/servicios') }}">Servicios</a></li>@endif
                    @if ($features['blog'] ?? true)<li><a href="{{ url('/blog') }}">Diario</a></li>@endif
                </ul>
            </div>
            <div>
                <h4>Contacto</h4>
                <ul class="t-aurora__footer-contact">
                    @if ($profile?->telefono_publico)<li><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}"><i class="fa-solid fa-phone"></i> {{ $profile->telefono_publico }}</a></li>@endif
                    @if ($telLimpioFooter)<li><a href="https://wa.me/{{ $telLimpioFooter }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></li>@endif
                    @if ($profile?->email_publico)<li><a href="mailto:{{ $profile->email_publico }}"><i class="fa-solid fa-envelope"></i> {{ $profile->email_publico }}</a></li>@endif
                    @if ($profile?->direccion)<li><i class="fa-solid fa-location-dot"></i> {{ $profile->direccion }}</li>@endif
                </ul>
            </div>
            @if (!empty($social) && (count(array_filter($social ?? [])) > 0))
                <div>
                    <h4>Sígueme</h4>
                    <div class="t-aurora__footer-social">
                        @if (!empty($social['instagram']))<a href="{{ $social['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>@endif
                        @if (!empty($social['facebook']))<a href="{{ $social['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>@endif
                        @if (!empty($social['linkedin']))<a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>@endif
                        @if (!empty($social['youtube']))<a href="{{ $social['youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>@endif
                        @if (!empty($social['tiktok']))<a href="{{ $social['tiktok'] }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="t-aurora__footer-bottom">
        <span>© {{ date('Y') }} {{ $user?->nombre }} {{ $user?->apellidos }}. <a href="https://victorroblesweb.es" target="_blank" rel="noopener" class="t-aurora__credit-link">Todos los derechos reservados.</a> · <a href="{{ route('public.privacidad') }}" class="t-aurora__credit-link">Política de privacidad</a></span>
        @if ($profile?->numero_colegiado)<span>Colegiada nº {{ $profile->numero_colegiado }}</span>@endif
    </div>
</footer>
