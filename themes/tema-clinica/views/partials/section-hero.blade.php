@php
    $citaUrl = ($features['reservas'] ?? true)
        ? (($themeMode ?? 'landing') === 'landing' ? '#cita' : url('/pide-cita'))
        : ($profile?->telefono_publico ? 'tel:' . preg_replace('/[^+0-9]/', '', $profile->telefono_publico) : '#cita');
    $aboutUrl = ($themeMode ?? 'landing') === 'landing' ? '#about' : url('/sobre-mi');
    $heroImg = theme_image('hero') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/hero.png'));

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
<section class="t-clinica__hero" id="home">
    <div class="t-clinica__hero-grid">
        <div class="t-clinica__hero-content">
            <span class="t-clinica__hero-badge"><i class="fa-solid fa-stethoscope"></i> {{ phrase('hero_badge', 'Psicología clínica') }}</span>
            <h1 class="t-clinica__hero-title">{{ phrase('hero_frase', 'Atención profesional, terapia humana') }}</h1>
            <p class="t-clinica__hero-lead">
                {{ $profile?->slogan ?? 'Te acompaño en tu proceso terapéutico con un enfoque cercano, científico y respetuoso.' }}
            </p>
            <div class="t-clinica__hero-actions">
                <a href="{{ $citaUrl }}" class="t-clinica__btn t-clinica__btn--primary"><i class="fa-solid fa-calendar-check"></i> Reserva tu cita</a>
                <a href="{{ $aboutUrl }}" class="t-clinica__btn t-clinica__btn--ghost">Conóceme</a>
            </div>
            <ul class="t-clinica__hero-features">
                <li><i class="fa-solid fa-check"></i> Sesiones online y presenciales</li>
                <li><i class="fa-solid fa-check"></i> Confidencialidad total</li>
                <li><i class="fa-solid fa-check"></i> Enfoque basado en evidencia</li>
            </ul>
        </div>
        <div class="t-clinica__hero-visual">
            <div class="t-clinica__hero-image">
                <img src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
            </div>
            @if ($profile?->numero_colegiado)
                <div class="t-clinica__hero-card">
                    <i class="fa-solid fa-id-badge"></i>
                    <div>
                        <strong>Colegiado nº {{ $profile->numero_colegiado }}</strong>
                        <span>Profesional registrada</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
