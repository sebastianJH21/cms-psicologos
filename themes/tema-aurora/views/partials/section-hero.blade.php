@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $citaUrl = ($features['reservas'] ?? true)
        ? ($isLanding ? '#cita' : url('/pide-cita'))
        : ($profile?->telefono_publico ? 'tel:' . preg_replace('/[^+0-9]/', '', $profile->telefono_publico) : '#cita');
    $aboutUrl = $isLanding ? '#about' : url('/sobre-mi');
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
<section class="t-aurora__hero" id="home">
    <div class="t-aurora__hero-inner">
        <div class="t-aurora__hero-eyebrow">
            <span class="t-aurora__hero-dot"></span>
            <span>{{ phrase('hero_overline', 'Un nuevo capítulo empieza hoy') }}</span>
        </div>

        <h1 class="t-aurora__hero-title">
            <span class="t-aurora__hero-h1">{{ $profile?->slogan ?: 'Acompañándote a reescribir tu historia' }}</span>
        </h1>

        <p class="t-aurora__hero-lead">{{ phrase('hero_frase', 'Soy ' . ($user?->nombre ?? '') . ', psicóloga. Te acompaño a comprender lo que sientes y a construir herramientas reales para tu día a día.') }}</p>

        <div class="t-aurora__hero-cta">
            <a href="{{ $citaUrl }}" class="t-aurora__btn t-aurora__btn--primary">
                <i class="fa-regular fa-calendar"></i> {{ phrase('hero_cta', 'Reservar primera sesión') }}
            </a>
            <a href="{{ $aboutUrl }}" class="t-aurora__btn t-aurora__btn--ghost">Conoce mi enfoque</a>
        </div>

    </div>

    @if ($heroImg)
        <div class="t-aurora__hero-visual">
            <div class="t-aurora__hero-img-frame">
                <img src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
            </div>
            <div class="t-aurora__hero-card t-aurora__hero-card--1">
                <i class="fa-solid fa-heart"></i>
                <span>Escucha activa</span>
            </div>
            <div class="t-aurora__hero-card t-aurora__hero-card--2">
                <i class="fa-solid fa-shield-heart"></i>
                <span>Espacio seguro</span>
            </div>
        </div>
    @endif
</section>
