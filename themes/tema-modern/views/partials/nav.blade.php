@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
@endphp
<header class="t-mod__nav">
    <div class="t-mod__nav-inner">
        @php $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
        <a href="{{ $home }}" class="t-mod__brand">
            @if (!empty($brandLogo['url']))
                <span class="t-mod__brand-mark"><img src="{{ $brandLogo['url'] }}" alt="Logo"></span>
            @elseif (!empty($brandLogo['icon']))
                <span class="t-mod__brand-mark"><i class="fa-solid {{ $brandLogo['icon'] }}"></i></span>
            @else
                <span class="t-mod__brand-dot"></span>
            @endif
            <span class="t-mod__name-mark">{{ $user?->nombre }} {{ $user?->apellidos }}</span>
        </a>
        <button class="t-mod__nav-toggle" aria-label="Menú"><i class="fa-solid fa-bars"></i></button>
        <ul class="t-mod__nav-list">
            <li><a href="{{ $home }}">Inicio</a></li>
            @if ($features['sobre_mi'] ?? true)<li><a href="{{ $about }}">Sobre mí</a></li>@endif
            @if ($features['servicios'] ?? true)<li><a href="{{ $services }}">Servicios</a></li>@endif
            @if ($features['blog'] ?? true)<li><a href="{{ $blog }}">Blog</a></li>@endif
            @if ($features['faq'] ?? true)<li><a href="{{ $faqUrl }}">FAQ</a></li>@endif
        </ul>
        @php
            $telLimpioMod = preg_replace('/[^0-9+]/', '', $profile?->telefono_publico ?? '');
            $telWaMod = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
            $waUrlMod = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWaMod ? 'https://wa.me/' . $telWaMod : null);
        @endphp
        <div class="t-mod__nav-actions">
            @if ($telLimpioMod)<a href="tel:{{ $telLimpioMod }}" class="t-mod__nav-icon" aria-label="Llamar"><i class="fa-solid fa-phone"></i></a>@endif
            @if ($waUrlMod)<a href="{{ $waUrlMod }}" target="_blank" rel="noopener" class="t-mod__nav-icon t-mod__nav-icon--wa" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>@endif
            @if ($features['reservas'] ?? true)<a href="{{ $citaUrl }}" class="t-mod__nav-cta">Pedir cita <i class="fa-solid fa-arrow-right"></i></a>@endif
        </div>
    </div>
</header>
