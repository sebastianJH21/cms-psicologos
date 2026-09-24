@php
    $citaUrl = ($features['reservas'] ?? true)
        ? (($themeMode ?? 'landing') === 'landing' ? '#cita' : url('/pide-cita'))
        : ($profile?->telefono_publico ? 'tel:' . preg_replace('/[^+0-9]/', '', $profile->telefono_publico) : '#cita');
    $heroImg = theme_image('hero') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : null);

    // Mejora carga imagenes
    $themeHero = theme_image('hero');
    
    // Verificamos si viene de theme-overrides (es decir, es imagen personalizada)
    $isCustomThemeHero = $themeHero && str_contains($themeHero, 'theme-overrides');

    if ($isCustomThemeHero) {
        $heroImg = $themeHero;
    } elseif ($profile?->foto_path) {
        $heroImg = asset('storage/' . $profile->foto_path);
    } else {
        $heroImg = theme_asset('assets/img/hero.png');
    }

@endphp
<section class="t-warm__hero" id="home">
    <div class="t-warm__hero-content">
        <span class="t-warm__hero-tag"><i class="fa-solid fa-sun"></i> {{ phrase('hero_overline', 'Tu espacio seguro') }}</span>
        <h1 class="t-warm__hero-title">{{ $profile?->slogan ?? 'Acompañamiento desde la calma' }}</h1>
        <p class="t-warm__hero-text">{{ phrase('hero_frase', 'Con ' . ($user?->nombre ?? '') . ', encuentras un espacio donde sentirte libre, escuchada y comprendida.') }}</p>
        <div class="t-warm__hero-actions">
            <a href="{{ $citaUrl }}" class="t-warm__btn t-warm__btn--primary"><i class="fa-regular fa-calendar"></i> {{ phrase('hero_cta', 'Reservar mi sesión') }}</a>
            @if ($profile?->telefono_publico)
                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}" class="t-warm__btn t-warm__btn--ghost"><i class="fa-solid fa-phone"></i> Llamar</a>
            @endif
        </div>
    </div>
    @if ($heroImg)
        <div class="t-warm__hero-photo">
            <img src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
        </div>
    @endif
    <svg class="t-warm__hero-wave" viewBox="0 0 1200 100" preserveAspectRatio="none">
        <path d="M0,50 C300,90 600,10 1200,60 L1200,100 L0,100 Z" fill="#fff"></path>
    </svg>
</section>
