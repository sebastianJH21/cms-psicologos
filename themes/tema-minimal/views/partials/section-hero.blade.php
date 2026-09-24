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
<section class="t-min__hero" id="home">
    <div class="t-min__hero-content">
        <p class="t-min__hero-overline">{{ $profile?->slogan ?? 'Psicología y bienestar' }}</p>
        <h1 class="t-min__hero-title">{{ $user?->nombre }} <em>{{ $user?->apellidos }}</em></h1>
        <p class="t-min__hero-subtitle">{{ phrase('hero_frase', 'Acompañamiento terapéutico claro, riguroso y humano.') }}</p>
        <a href="{{ $citaUrl }}" class="t-min__hero-cta">Reserva una sesión <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    @if ($heroImg)
        <div class="t-min__hero-image">
            <img src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
        </div>
    @endif
</section>
