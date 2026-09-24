@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
    $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null];
@endphp
<header class="t-aurora__nav" id="t-aurora-nav">
    <div class="t-aurora__nav-inner">
        <a href="{{ $home }}" class="t-aurora__brand">
            <span class="t-aurora__brand-mark">
                @if (!empty($brandLogo['url']))
                    <img src="{{ $brandLogo['url'] }}" alt="Logo">
                @elseif (!empty($brandLogo['icon']))
                    <i class="fa-solid {{ $brandLogo['icon'] }}"></i>
                @else
                    <i class="fa-solid fa-feather-pointed"></i>
                @endif
            </span>
            <span class="t-aurora__brand-text">
                <strong>{{ $user?->nombre }} {{ $user?->apellidos }}</strong>
                <small>{{ phrase('hero_badge', 'Psicología consciente') }}</small>
            </span>
        </a>

        <button class="t-aurora__nav-toggle" aria-label="Menú"><i class="fa-solid fa-bars"></i></button>

        <ul class="t-aurora__nav-list">
            <li><a href="{{ $home }}">Inicio</a></li>
            @if ($features['sobre_mi'] ?? true)<li><a href="{{ $about }}">Sobre mí</a></li>@endif
            @if ($features['servicios'] ?? true)<li><a href="{{ $services }}">Servicios</a></li>@endif
            @if ($features['blog'] ?? true)<li><a href="{{ $blog }}">Diario</a></li>@endif
            @if ($features['faq'] ?? true)<li><a href="{{ $faqUrl }}">FAQ</a></li>@endif
        </ul>

        @php
            $telLimpioAur = preg_replace('/[^0-9+]/', '', $profile?->telefono_publico ?? '');
            $telWaAur = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
            $waUrlAur = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWaAur ? 'https://wa.me/' . $telWaAur : null);
        @endphp
        <div class="t-aurora__nav-actions">
            @if ($telLimpioAur)<a href="tel:{{ $telLimpioAur }}" class="t-aurora__nav-icon" aria-label="Llamar"><i class="fa-solid fa-phone"></i></a>@endif
            @if ($waUrlAur)<a href="{{ $waUrlAur }}" target="_blank" rel="noopener" class="t-aurora__nav-icon t-aurora__nav-icon--wa" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>@endif
            @if ($features['reservas'] ?? true)
                <a href="{{ $citaUrl }}" class="t-aurora__nav-cta">
                    Pedir cita
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endif
        </div>
    </div>
</header>
