@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $faqUrl = $isLanding ? '/#faq' : url('/preguntas-frecuentes');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
@endphp

<nav class="layout__nav-mobile">
    <div class="nav-mobile__left">
        <header class="nav-mobile__header-mobile">
            <header class="header-mobile__left">
                <div class="header-mobile__container-img">
                    <img class="header-mobile__img" src="{{ theme_asset('assets/img/logo.png', 'tema-base') }}" alt="Logo">
                </div>
                <div class="header-mobile__content">
                    <h1 class="header-mobile__title">{{ $user?->nombre }} {{ $user?->apellidos }}</h1>
                    <h2 class="header-mobile__subtitle">{{ $profile?->slogan ?? 'Psicología' }}</h2>
                </div>
            </header>

            <button class="header__btn-close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <ul class="nav-mobile__list-mobile">
            <li class="list-mobile__item"><a href="{{ $home }}" class="list-mobile__link">Inicio</a></li>
            @if ($features['sobre_mi'] ?? true)
                <li class="list-mobile__item"><a href="{{ $about }}" class="list-mobile__link">Sobre mí</a></li>
            @endif
            @if ($features['servicios'] ?? true)
                <li class="list-mobile__item"><a href="{{ $services }}" class="list-mobile__link">Servicios</a></li>
            @endif
            @if ($features['blog'] ?? true)
                <li class="list-mobile__item"><a href="{{ $blog }}" class="list-mobile__link">Blog</a></li>
            @endif
            @if ($features['faq'] ?? true)
                <li class="list-mobile__item"><a href="{{ $faqUrl }}" class="list-mobile__link">FAQ</a></li>
            @endif
            @if ($features['reservas'] ?? true)
                <li class="list-mobile__item"><a href="{{ $citaUrl }}" class="list-mobile__link">Pide cita</a></li>
            @endif
        </ul>
    </div>

    <div class="nav-mobile__contact-mobile">
        @if ($profile?->email_publico)
            <div class="contact-mobile__container">
                <i class="fa-solid fa-envelope"></i>
                <h4 class="contact-mobile__email">{{ $profile->email_publico }}</h4>
            </div>
        @endif
        @if ($profile?->telefono_publico)
            <div class="contact-mobile__container">
                <i class="fa-solid fa-phone"></i>
                <h4 class="contact-mobile__phone">{{ $profile->telefono_publico }}</h4>
            </div>
        @endif
    </div>

    <ul class="nav-mobile__list-m-social">
        @php
            $socialIcons = [
                'twitter' => 'fa-brands fa-twitter',
                'facebook' => 'fa-brands fa-facebook',
                'instagram' => 'fa-brands fa-instagram',
                'tiktok' => 'fa-brands fa-tiktok',
                'linkedin' => 'fa-brands fa-linkedin',
                'youtube' => 'fa-brands fa-youtube',
            ];
        @endphp
        @foreach ($socialIcons as $key => $icon)
            @if (!empty($social[$key]))
                <li class="list-m-social__item">
                    <a href="{{ $social[$key] }}" target="_blank" rel="noopener" class="list-m-social__link">
                        <i class="{{ $icon }} list-m-social__ico"></i>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>
