@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $home = $isLanding ? '/#home' : url('/');
    $about = $isLanding ? '/#about' : url('/sobre-mi');
    $services = $isLanding ? '/#services' : url('/servicios');
    $blog = $isLanding ? '/#blog' : url('/blog');
    $citaUrl = $isLanding ? '/#cita' : url('/pide-cita');
    $telBaseFooter = preg_replace('/[^0-9]/', '', $profile?->telefono_publico ?? '');
    $waBaseFooter = !empty($social['whatsapp']) ? $social['whatsapp'] : ($telBaseFooter ? 'https://wa.me/' . $telBaseFooter : null);
@endphp

<footer class="layout__footer">
    <div class="footer__footer-top">
        <div class="footer__container">
            <div class="footer-top__left">
                <div class="footer-top__container-ico">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="footer-top__schedule">
                    <h4 class="schedule__text">Horario:</h4>
                    <p class="schedule__time">Consulta los horarios disponibles al pedir cita</p>
                </div>
            </div>

            <div class="footer-top__social">
                <p class="social__label">Sígueme en:</p>
                <ul class="social__list-social">
                    @php
                        $socialIcons = [
                            'twitter' => 'fa-brands fa-twitter',
                            'facebook' => 'fa-brands fa-facebook',
                            'instagram' => 'fa-brands fa-instagram',
                            'tiktok' => 'fa-brands fa-tiktok',
                            'linkedin' => 'fa-brands fa-linkedin',
                            'youtube' => 'fa-brands fa-youtube',
                            'whatsapp' => 'fa-brands fa-whatsapp',
                        ];
                    @endphp
                    @foreach ($socialIcons as $key => $icon)
                        @if (!empty($social[$key]))
                            <li class="list-social__item">
                                <a href="{{ $social[$key] }}" target="_blank" rel="noopener" class="list-social__link" aria-label="{{ ucfirst($key) }}">
                                    <i class="{{ $icon }} list-social__ico"></i>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="footer__footer-middle">
        <div class="footer__container">
            <div class="footer-middle__container">
                @php $brandLogoFooter = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
                <div class="footer-middle__logo">
                    @if (!empty($brandLogoFooter['url']))
                        <img class="logo__img" src="{{ $brandLogoFooter['url'] }}" alt="Logo">
                    @elseif (!empty($brandLogoFooter['icon']))
                        <i class="fa-solid {{ $brandLogoFooter['icon'] }} logo__img-icon"></i>
                    @else
                        <img class="logo__img" src="{{ theme_asset('assets/img/logo.png', 'tema-base') }}" alt="Logo">
                    @endif
                    <div class="logo__container-text">
                        <h1 class="logo___title">{{ $user?->nombre }} {{ $user?->apellidos }}</h1>
                        <p class="logo___subtitle">{{ $profile?->slogan ?? 'Psicología' }}</p>
                    </div>
                </div>

                @if ($profile?->numero_colegiado)
                    <p class="footer-middle__description" style="margin-top: 1rem;">
                        <strong>N.º colegiado/a:</strong> {{ $profile->numero_colegiado }}
                    </p>
                @endif
            </div>

            <div class="footer-middle__container">
                <h3 class="footer-middle__title">Explorar</h3>
                <ul class="footer-middle__list-explore">
                    <li class="list-explore__item"><a href="{{ $home }}" class="list-explore__link">Inicio</a></li>
                    @if ($features['sobre_mi'] ?? true)
                        <li class="list-explore__item"><a href="{{ $about }}" class="list-explore__link">Sobre mí</a></li>
                    @endif
                    @if ($features['servicios'] ?? true)
                        <li class="list-explore__item"><a href="{{ $services }}" class="list-explore__link">Servicios</a></li>
                    @endif
                    @if ($features['blog'] ?? true)
                        <li class="list-explore__item"><a href="{{ $blog }}" class="list-explore__link">Blog</a></li>
                    @endif
                    @if ($features['reservas'] ?? true)
                        <li class="list-explore__item"><a href="{{ $citaUrl }}" class="list-explore__link">Pide cita</a></li>
                    @endif
                </ul>
            </div>

            <div class="footer-middle__container">
                <h3 class="footer-middle__title">Contacto</h3>
                <ul class="footer-middle__list-contact">
                    @if ($profile?->direccion)
                        <li class="list-contact__item">
                            <div class="list-contact__container-ico"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="list-contact__content">
                                <p class="list-contact__label">Visitar consulta</p>
                                <h4 class="list-contact__data">{{ $profile->direccion }}</h4>
                            </div>
                        </li>
                    @endif
                    @if ($profile?->email_publico)
                        <li class="list-contact__item">
                            <div class="list-contact__container-ico"><i class="fa-solid fa-envelope"></i></div>
                            <div class="list-contact__content">
                                <p class="list-contact__label">Email</p>
                                <h4 class="list-contact__data"><a href="mailto:{{ $profile->email_publico }}">{{ $profile->email_publico }}</a></h4>
                            </div>
                        </li>
                    @endif
                    @if ($profile?->telefono_publico)
                        <li class="list-contact__item">
                            <div class="list-contact__container-ico"><i class="fa-solid fa-phone"></i></div>
                            <div class="list-contact__content">
                                <p class="list-contact__label">Teléfono</p>
                                <h4 class="list-contact__data"><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile->telefono_publico) }}">{{ $profile->telefono_publico }}</a></h4>
                            </div>
                        </li>
                        @if ($waBaseFooter)
                            <li class="list-contact__item">
                                <div class="list-contact__container-ico"><i class="fa-brands fa-whatsapp"></i></div>
                                <div class="list-contact__content">
                                    <p class="list-contact__label">WhatsApp</p>
                                    <h4 class="list-contact__data"><a href="{{ $waBaseFooter }}" target="_blank" rel="noopener">Hablar por WhatsApp</a></h4>
                                </div>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="footer__footer-bottom">
        <div class="footer__container">
            <p class="footer-bottom__copyright">© {{ date('Y') }} {{ $user?->nombre }} {{ $user?->apellidos }}. <a href="https://victorroblesweb.es" target="_blank" rel="noopener" class="footer-bottom__credit-link">Todos los derechos reservados.</a> · <a href="{{ route('public.privacidad') }}" class="footer-bottom__credit-link">Política de privacidad</a></p>
        </div>
    </div>
</footer>
