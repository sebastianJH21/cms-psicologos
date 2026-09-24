@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
    $telefonoLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
    $telWaBase = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waUrlBase = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telWaBase ? 'https://wa.me/' . $telWaBase : null);
@endphp

<nav class="layout__nav">
    <div class="nav__left">
        @php $brandLogo = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
        <header class="nav__header">
            <div class="header__container-img">
                @if (!empty($brandLogo['url']))
                    <img class="header__img" src="{{ $brandLogo['url'] }}" alt="Logo">
                @elseif (!empty($brandLogo['icon']))
                    <i class="fa-solid {{ $brandLogo['icon'] }} header__img-icon"></i>
                @else
                    <img class="header__img" src="{{ theme_asset('assets/img/logo.png', 'tema-base') }}" alt="Logo">
                @endif
            </div>
            <div class="header__content">
                <h1 class="header__title">{{ $user?->nombre }} {{ $user?->apellidos }}</h1>
                <h2 class="header__subtitle">{{ $profile?->slogan ?? 'Psicología y asesoramiento' }}</h2>
            </div>
        </header>

        <ul class="nav__list-nav">
            <li class="list-nav__item"><a href="{{ $home }}" class="list-nav__link">Inicio</a></li>
            @if ($features['sobre_mi'] ?? true)
                <li class="list-nav__item"><a href="{{ $about }}" class="list-nav__link">Sobre mí</a></li>
            @endif
            @if ($features['servicios'] ?? true)
                <li class="list-nav__item"><a href="{{ $services }}" class="list-nav__link">Servicios</a></li>
            @endif
            @if ($features['blog'] ?? true)
                <li class="list-nav__item"><a href="{{ $blog }}" class="list-nav__link">Blog</a></li>
            @endif
            @if ($features['faq'] ?? true)
                <li class="list-nav__item"><a href="{{ $faqUrl }}" class="list-nav__link">FAQ</a></li>
            @endif
        </ul>
    </div>

    <div class="nav__btn-menu-mobile">
        <i class="fa-solid fa-bars"></i>
    </div>

    <div class="nav__contact">
        @if ($profile?->telefono_publico)
            <a class="contact__container-ico" href="tel:{{ $telefonoLimpio }}" title="Teléfono">
                <i class="fa-solid fa-phone-volume contact__phone-ico"></i>
            </a>
            <div class="contact__phone">
                <p class="phone__title">¿Tienes alguna pregunta?</p>
                <h4 class="phone__number">{{ $profile->telefono_publico }}</h4>
            </div>
        @endif
        <div class="contact__extra">
            @if ($profile?->email_publico)
                <a class="contact__container-ico" href="mailto:{{ $profile->email_publico }}" title="Correo electrónico">
                    <i class="fa-regular fa-envelope contact__email-ico"></i>
                </a>
            @endif
            @if ($waUrlBase)
                <a class="contact__container-ico contact__container-ico--wa" href="{{ $waUrlBase }}" target="_blank" rel="noopener" title="WhatsApp">
                    <i class="fa-brands fa-whatsapp contact__whatsapp-ico"></i>
                </a>
            @endif
            @if ($features['reservas'] ?? true)
                <a class="contact__cta-cita" href="{{ $citaUrl }}" title="Pedir cita">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Pedir cita</span>
                </a>
            @endif
        </div>
    </div>
</nav>
