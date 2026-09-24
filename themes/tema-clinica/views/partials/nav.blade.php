@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
@endphp

<header class="t-clinica__nav">
    <div class="t-clinica__nav-container">
        @php $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
        <a href="{{ $home }}" class="t-clinica__brand">
            <div class="t-clinica__brand-mark">
                @if (!empty($brandLogo['url']))
                    <img src="{{ $brandLogo['url'] }}" alt="Logo">
                @elseif (!empty($brandLogo['icon']))
                    <i class="fa-solid {{ $brandLogo['icon'] }}"></i>
                @else
                    <i class="fa-solid fa-leaf"></i>
                @endif
            </div>
            <div class="t-clinica__brand-text">
                <strong>{{ $user?->nombre }} {{ $user?->apellidos }}</strong>
                <span>{{ $profile?->slogan ?? 'Psicología clínica' }}</span>
            </div>
        </a>

        <button class="t-clinica__nav-toggle" aria-label="Abrir menú"><i class="fa-solid fa-bars"></i></button>

        <ul class="t-clinica__nav-list">
            <li><a href="{{ $home }}">Inicio</a></li>
            @if ($features['sobre_mi'] ?? true)
                <li><a href="{{ $about }}">Sobre mí</a></li>
            @endif
            @if ($features['servicios'] ?? true)
                <li><a href="{{ $services }}">Servicios</a></li>
            @endif
            @if ($features['blog'] ?? true)
                <li><a href="{{ $blog }}">Blog</a></li>
            @endif
            @if ($features['faq'] ?? true)
                <li><a href="{{ $faqUrl }}">FAQ</a></li>
            @endif
        </ul>
        @php
            $telLimpioCli = preg_replace('/[^0-9+]/', '', $profile?->telefono_publico ?? '');
            $telWaCli = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
            $waUrlCli = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWaCli ? 'https://wa.me/' . $telWaCli : null);
        @endphp
        <div class="t-clinica__nav-actions">
            @if ($telLimpioCli)<a href="tel:{{ $telLimpioCli }}" class="t-clinica__nav-icon" aria-label="Llamar"><i class="fa-solid fa-phone"></i></a>@endif
            @if ($waUrlCli)<a href="{{ $waUrlCli }}" target="_blank" rel="noopener" class="t-clinica__nav-icon t-clinica__nav-icon--wa" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>@endif
            @if ($features['reservas'] ?? true)<a href="{{ $citaUrl }}" class="t-clinica__nav-cta">Pedir cita</a>@endif
        </div>
    </div>
</header>
