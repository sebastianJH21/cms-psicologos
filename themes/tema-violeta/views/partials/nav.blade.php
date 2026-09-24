@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
@endphp

<header class="t-violeta__nav">
    <div class="t-violeta__nav-container">
        @php $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
        <a href="{{ $home }}" class="t-violeta__brand">
            <div class="t-violeta__brand-mark">
                @if (!empty($brandLogo['url']))
                    <img src="{{ $brandLogo['url'] }}" alt="Logo">
                @elseif (!empty($brandLogo['icon']))
                    <i class="fa-solid {{ $brandLogo['icon'] }}"></i>
                @else
                    <i class="fa-solid fa-leaf"></i>
                @endif
            </div>
            <div class="t-violeta__brand-text">
                <strong>{{ $user?->nombre }} {{ $user?->apellidos }}</strong>
                <span>{{ $profile?->slogan ?? 'Psicología clínica' }}</span>
            </div>
        </a>

        <button class="t-violeta__nav-toggle" aria-label="Abrir menú"><i class="fa-solid fa-bars"></i></button>

        <ul class="t-violeta__nav-list">
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
            $telLimpio_tema_violeta = preg_replace('/[^0-9+]/', '', $profile?->telefono_publico ?? '');
            $telWa_tema_violeta = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
            $waUrl_tema_violeta = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWa_tema_violeta ? 'https://wa.me/' . $telWa_tema_violeta : null);
        @endphp
        <div class="t-violeta__nav-actions">
            @if ($telLimpio_tema_violeta)<a href="tel:{{ $telLimpio_tema_violeta }}" class="t-violeta__nav-icon" aria-label="Llamar"><i class="fa-solid fa-phone"></i></a>@endif
            @if ($waUrl_tema_violeta)<a href="{{ $waUrl_tema_violeta }}" target="_blank" rel="noopener" class="t-violeta__nav-icon t-violeta__nav-icon--wa" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>@endif
            @if ($features['reservas'] ?? true)<a href="{{ $citaUrl }}" class="t-violeta__nav-cta">Pedir cita</a>@endif
        </div>
    </div>
</header>
