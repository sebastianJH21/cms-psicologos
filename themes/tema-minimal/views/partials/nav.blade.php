@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
@endphp
<header class="t-min__nav">
    @php $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
    <a href="{{ $home }}" class="t-min__brand">
        @if (!empty($brandLogo['url']))
            <span class="t-min__brand-mark"><img src="{{ $brandLogo['url'] }}" alt="Logo"></span>
        @elseif (!empty($brandLogo['icon']))
            <span class="t-min__brand-mark"><i class="fa-solid {{ $brandLogo['icon'] }}"></i></span>
        @endif
        <span class="t-min__brand-name">{{ $user?->nombre }} {{ $user?->apellidos }}</span>
    </a>
    <button class="t-min__nav-toggle" aria-label="Menú"><i class="fa-solid fa-bars"></i></button>
    <ul class="t-min__nav-list">
        <li><a href="{{ $home }}">Inicio</a></li>
        @if ($features['sobre_mi'] ?? true)<li><a href="{{ $about }}">Sobre mí</a></li>@endif
        @if ($features['servicios'] ?? true)<li><a href="{{ $services }}">Servicios</a></li>@endif
        @if ($features['blog'] ?? true)<li><a href="{{ $blog }}">Blog</a></li>@endif
        @if ($features['faq'] ?? true)<li><a href="{{ $faqUrl }}">FAQ</a></li>@endif
    </ul>
    @php
        $telLimpioMin = preg_replace('/[^0-9+]/', '', $profile?->telefono_publico ?? '');
        $telWaMin = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
        $waUrlMin = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWaMin ? 'https://wa.me/' . $telWaMin : null);
    @endphp
    <div class="t-min__nav-actions">
        @if ($telLimpioMin)<a href="tel:{{ $telLimpioMin }}" class="t-min__nav-icon" aria-label="Llamar"><i class="fa-solid fa-phone"></i></a>@endif
        @if ($waUrlMin)<a href="{{ $waUrlMin }}" target="_blank" rel="noopener" class="t-min__nav-icon t-min__nav-icon--wa" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>@endif
        @if ($features['reservas'] ?? true)<a href="{{ $citaUrl }}" class="t-min__nav-cta">Pedir cita</a>@endif
    </div>
</header>
