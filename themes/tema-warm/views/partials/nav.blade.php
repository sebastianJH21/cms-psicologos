@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
@endphp
@php $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
<header class="t-warm__nav">
    <div class="t-warm__nav-inner">
        <a href="{{ $home }}" class="t-warm__brand">
            @if (!empty($brandLogo['url']))
                <img src="{{ $brandLogo['url'] }}" alt="Logo" class="t-warm__brand-logo-img">
            @elseif (!empty($brandLogo['icon']))
                <i class="fa-solid {{ $brandLogo['icon'] }} t-warm__brand-flower"></i>
            @else
                <span class="t-warm__brand-flower">🌸</span>
            @endif
            <div>
                <strong>{{ $user?->nombre }} {{ $user?->apellidos }}</strong>
                <em>{{ $profile?->slogan ?? 'Psicología y bienestar' }}</em>
            </div>
        </a>
        <button class="t-warm__nav-toggle" aria-label="Menú"><i class="fa-solid fa-bars"></i></button>
        <ul class="t-warm__nav-list">
            <li><a href="{{ $home }}">Inicio</a></li>
            @if ($features['sobre_mi'] ?? true)<li><a href="{{ $about }}">Sobre mí</a></li>@endif
            @if ($features['servicios'] ?? true)<li><a href="{{ $services }}">Servicios</a></li>@endif
            @if ($features['blog'] ?? true)<li><a href="{{ $blog }}">Blog</a></li>@endif
            @if ($features['faq'] ?? true)<li><a href="{{ $faqUrl }}">FAQ</a></li>@endif
        </ul>
        @php
            $telLimpioWarm = preg_replace('/[^0-9+]/', '', $profile?->telefono_publico ?? '');
            $telWaLimpio = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
            $waUrl = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWaLimpio ? 'https://wa.me/' . $telWaLimpio : null);
        @endphp
        <div class="t-warm__nav-actions nav__contact">
            @if ($profile?->email_publico)
                <a href="mailto:{{ $profile->email_publico }}" class="t-warm__nav-icon" aria-label="Enviar email" title="Email"><i class="fa-solid fa-envelope"></i></a>
            @endif
            @if ($telLimpioWarm)
                <a href="tel:{{ $telLimpioWarm }}" class="t-warm__nav-icon" aria-label="Llamar" title="Llamar"><i class="fa-solid fa-phone"></i></a>
            @endif
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="t-warm__nav-icon t-warm__nav-icon--wa" aria-label="WhatsApp" title="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            @endif
            @if ($features['reservas'] ?? true)
                <a href="{{ $citaUrl }}" class="t-warm__nav-cta"><i class="fa-regular fa-calendar"></i> Pedir cita</a>
            @endif
        </div>
    </div>
</header>
