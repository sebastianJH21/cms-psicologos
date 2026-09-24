@php
    $citaUrl = ($features['reservas'] ?? true)
        ? (($themeMode ?? 'landing') === 'landing' ? '#cita' : url('/pide-cita'))
        : ($profile?->telefono_publico ? 'tel:' . preg_replace('/[^+0-9]/', '', $profile->telefono_publico) : '#cita');
    $aboutUrl = ($themeMode ?? 'landing') === 'landing' ? '#about' : url('/sobre-mi');
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
<section class="t-mod__hero" id="home">
    <div class="t-mod__hero-glass">
        <div class="t-mod__hero-content">
            <span class="t-mod__hero-tag"><span class="t-mod__pulse"></span> Sesiones disponibles esta semana</span>
            <h1 class="t-mod__hero-title">{{ $profile?->slogan ?? 'Tu bienestar mental, mi prioridad' }}</h1>
            <p class="t-mod__hero-text">{{ phrase('hero_frase', 'Hola, soy ' . trim(($user?->nombre ?? '') . ' ' . ($user?->apellidos ?? '')) . '. Combino terapia humanista con técnicas modernas para acompañarte en tu proceso.') }}</p>
            <div class="t-mod__hero-actions">
                <a href="{{ $citaUrl }}" class="t-mod__btn t-mod__btn--primary">Reservar sesión <i class="fa-solid fa-sparkles"></i></a>
                <a href="{{ $aboutUrl }}" class="t-mod__btn t-mod__btn--ghost">Conóceme</a>
            </div>
        </div>
        @if ($heroImg)
            <div class="t-mod__hero-photo">
                <img src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
            </div>
        @endif
    </div>
</section>
