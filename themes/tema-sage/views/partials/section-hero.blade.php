@php
    $citaUrl = ($features['reservas'] ?? true)
        ? (($themeMode ?? 'landing') === 'landing' ? '#cita' : url('/pide-cita'))
        : ($profile?->telefono_publico ? 'tel:' . preg_replace('/[^+0-9]/', '', $profile->telefono_publico) : '#cita');
    $aboutUrl = ($themeMode ?? 'landing') === 'landing' ? '#about' : url('/sobre-mi');
    $heroImg = theme_image('hero') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/hero.png'));
@endphp
<section class="t-sage__hero" id="home">
    <div class="t-sage__hero-grid">
        <div class="t-sage__hero-content">
            <span class="t-sage__hero-badge"><i class="fa-solid fa-stethoscope"></i> {{ phrase('hero_badge', 'Psicología clínica') }}</span>
            <h1 class="t-sage__hero-title">{{ phrase('hero_frase', 'Atención profesional, terapia humana') }}</h1>
            <p class="t-sage__hero-lead">
                {{ $profile?->slogan ?? 'Te acompaño en tu proceso terapéutico con un enfoque cercano, científico y respetuoso.' }}
            </p>
            <div class="t-sage__hero-actions">
                <a href="{{ $citaUrl }}" class="t-sage__btn t-sage__btn--primary"><i class="fa-solid fa-calendar-check"></i> Reserva tu cita</a>
                <a href="{{ $aboutUrl }}" class="t-sage__btn t-sage__btn--ghost">Conóceme</a>
            </div>
            <ul class="t-sage__hero-features">
                <li><i class="fa-solid fa-check"></i> Sesiones online y presenciales</li>
                <li><i class="fa-solid fa-check"></i> Confidencialidad total</li>
                <li><i class="fa-solid fa-check"></i> Enfoque basado en evidencia</li>
            </ul>
        </div>
        <div class="t-sage__hero-visual">
            <div class="t-sage__hero-image">
                <img src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
            </div>
            @if ($profile?->numero_colegiado)
                <div class="t-sage__hero-card">
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
