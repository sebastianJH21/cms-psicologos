@php
    $citaUrl = ($themeMode ?? 'landing') === 'landing' ? '#cita' : url('/pide-cita');
    $telMinFooter = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waMinFooter = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telMinFooter ? 'https://wa.me/' . $telMinFooter : null);
    $minFooterLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null];
@endphp
<footer class="t-min__footer">
    <div class="t-min__footer-inner">
        <div class="t-min__footer-top">
            <div>
                <div class="t-min__footer-brand">
                    <span class="t-min__footer-brand-mark">
                        @if (!empty($minFooterLogo['url']))<img src="{{ $minFooterLogo['url'] }}" alt="Logo">
                        @elseif (!empty($minFooterLogo['icon']))<i class="fa-solid {{ $minFooterLogo['icon'] }}"></i>
                        @else<i class="fa-solid fa-leaf"></i>@endif
                    </span>
                    <h4 class="t-min__footer-name">{{ $user?->nombre }} {{ $user?->apellidos }}</h4>
                </div>
                <p class="t-min__footer-slogan">{{ $profile?->slogan }}</p>
                @if ($profile?->numero_colegiado)
                    <p class="t-min__footer-meta">Colegiada nº {{ $profile->numero_colegiado }}</p>
                @endif
            </div>
            <div class="t-min__footer-contact">
                @if ($profile?->telefono_publico)
                    <p><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}">{{ $profile->telefono_publico }}</a></p>
                    @if ($waMinFooter)
                        <p><a href="{{ $waMinFooter }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>
                    @endif
                @endif
                @if ($profile?->email_publico)<p><a href="mailto:{{ $profile->email_publico }}">{{ $profile->email_publico }}</a></p>@endif
                @if ($profile?->direccion)<p>{{ $profile->direccion }}</p>@endif
            </div>
        </div>

        <div class="t-min__footer-social">
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

        <div class="t-min__footer-bottom">
            <span>© {{ date('Y') }} {{ $user?->nombre }} {{ $user?->apellidos }}</span>
            <a href="https://victorroblesweb.es" target="_blank" rel="noopener" class="t-min__credit-link">Todos los derechos reservados</a>
            <a href="{{ route('public.privacidad') }}" class="t-min__credit-link">Política de privacidad</a>
        </div>
    </div>
</footer>
