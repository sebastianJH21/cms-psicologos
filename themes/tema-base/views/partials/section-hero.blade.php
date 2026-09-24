@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $citaUrl = ($features['reservas'] ?? true)
        ? ($isLanding ? '#cita' : url('/pide-cita'))
        : ($profile?->telefono_publico ? 'tel:' . preg_replace('/[^+0-9]/', '', $profile->telefono_publico) : '#');
@endphp

<div class="layout__banner" id="home">
    <div class="banner__content">
        <div class="banner__sandwich">
            <p class="sandwich__text">{{ phrase('hero_overline', $profile?->slogan ?: 'Acompañándote en tu camino de bienestar') }}</p>
        </div>
        <h1 class="banner__title">
            {{ phrase('hero_frase', $profile?->slogan ?: 'Estamos preparadas para escuchar tus problemas') }}
        </h1>

        <div class="banner__appointment">
            <a class="appointment__btn" href="{{ $citaUrl }}">{{ phrase('hero_cta', 'Pide tu cita') }}</a>
                
            <!--<p class="appointment__psico-name">{{ $user?->nombre }} - Psicóloga</p>-->
        </div>
    </div>

    @php
        $heroImg = theme_image('hero') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/psicologa.png'));
   
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
    <div class="banner__container-img">
        <img class="banner__img" src="{{ $heroImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
    </div>

    <div class="banner__shapes">
        <div class="shapes__shape1">
            <img class="shape1__img" src="{{ theme_asset('assets/img/shape1.png') }}" alt="">
        </div>
        <div class="shapes__shape2">
            <img class="shape2__img" src="{{ theme_asset('assets/img/shape2.png') }}" alt="">
        </div>
    </div>
</div>
