@php
    use App\Services\ImagenSlotResolver;
    $slug = $theme['slug'] ?? 'tema-base';
    $resolver = app(ImagenSlotResolver::class);
    $backgroundNavUrl = route('theme.asset', [$slug, 'assets/img/background-nav.jpg']);
    $heroUrl = $resolver->resolve($slug, 'hero');
    $sobreMiUrl = $resolver->resolve($slug, 'sobre-mi');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview · {{ $theme['name'] }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        :root {
            --color-primary:     {{ $theme['color_palette']['primary']    ?? '#976147' }};
            --color-secondary:   {{ $theme['color_palette']['secondary']  ?? '#5b4034' }};
            --color-accent:      {{ $theme['color_palette']['accent']     ?? '#c0a090' }};
            --color-bg:          {{ $theme['color_palette']['bg']         ?? '#f6f2f0' }};
            --color-bg-alt:      {{ $theme['color_palette']['bg_alt']     ?? '#eee8e5' }};
            --color-text:        {{ $theme['color_palette']['text']       ?? '#1a1414' }};
            --color-text-light:  {{ $theme['color_palette']['text_light'] ?? '#736b6b' }};
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 10px; scroll-behavior: smooth; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            font-size: 1.5rem;
            line-height: 1.6;
        }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }
        img { display: block; max-width: 100%; }

        /* ── BADGE PREVIEW ── */
        .preview-badge {
            position: fixed;
            top: 1rem; right: 1rem;
            background: rgba(0,0,0,.72);
            color: #fff;
            padding: .6rem 1.6rem;
            border-radius: 10rem;
            font-size: 1.2rem;
            z-index: 999;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        /* ── NAV ── */
        .nav {
            width: 100%;
            padding: 1.8rem 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--color-bg-alt);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .nav__left {
            display: flex;
            align-items: center;
            gap: 5rem;
        }
        .nav__header {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
        .nav__logo-img {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--color-primary);
        }
        .nav__logo-placeholder {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
        }
        .nav__name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-text);
            line-height: 1.2;
        }
        .nav__role {
            font-size: 1.1rem;
            color: var(--color-text-light);
        }
        .nav__links {
            display: flex;
            gap: 3rem;
        }
        .nav__links a {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--color-text-light);
            transition: color .2s;
        }
        .nav__links a:hover { color: var(--color-primary); }
        .nav__contact {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
        .nav__contact-icon {
            width: 4.2rem;
            height: 4.2rem;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 1.6rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            transition: background .2s, color .2s;
        }
        .nav__cta-btn {
            background: var(--color-primary);
            color: #fff;
            border: none;
            padding: 1rem 2.4rem;
            border-radius: 10rem;
            font-size: 1.4rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* ── HERO / BANNER ── */
        .banner {
            position: relative;
            width: 100%;
            background-color: var(--color-bg-alt);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8rem 4rem 0;
            overflow: hidden;
            min-height: 52rem;
        }
        .banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ $backgroundNavUrl }}');
            background-size: cover;
            background-position: center;
            opacity: .18;
        }
        .banner__inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1100px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 4rem;
        }
        .banner__content {
            flex: 1;
            padding-bottom: 4rem;
        }
        .banner__eyebrow {
            display: inline-block;
            background: var(--color-primary);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
            padding: .4rem 1.4rem;
            border-radius: 10rem;
            margin-bottom: 1.6rem;
        }
        .banner__title {
            font-size: 4.8rem;
            font-weight: 700;
            color: var(--color-text);
            line-height: 1.15;
            margin-bottom: 1.6rem;
        }
        .banner__title span {
            color: var(--color-primary);
        }
        .banner__slogan {
            font-size: 1.6rem;
            color: var(--color-text-light);
            margin-bottom: 3rem;
            max-width: 46rem;
            line-height: 1.7;
        }
        .banner__btns {
            display: flex;
            gap: 1.4rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 1.2rem 2.8rem;
            border-radius: 10rem;
            font-size: 1.4rem;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid transparent;
            transition: background .2s, color .2s;
        }
        .btn--primary {
            background: var(--color-primary);
            color: #fff;
        }
        .btn--outline {
            background: transparent;
            border-color: var(--color-primary);
            color: var(--color-primary);
        }
        .banner__contact {
            margin-top: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.5rem;
            color: var(--color-text-light);
        }
        .banner__contact i {
            color: var(--color-primary);
        }
        .banner__img-wrap {
            position: relative;
            z-index: 2;
            width: 36rem;
            flex-shrink: 0;
            align-self: flex-end;
        }
        .banner__img-wrap img {
            width: 100%;
            object-fit: contain;
            max-height: 46rem;
        }
        .banner__img-placeholder {
            width: 100%;
            height: 40rem;
            background: var(--color-accent);
            border-radius: 3rem 3rem 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 8rem;
        }
        .banner__shape1 {
            position: absolute;
            left: -2rem;
            top: 30%;
            width: 12rem;
            opacity: .6;
            animation: float 4s ease-in-out infinite alternate;
            z-index: 1;
        }
        @keyframes float {
            from { transform: translateY(0); }
            to   { transform: translateY(-20px); }
        }

        /* ── TERAPIAS ── */
        .terapias {
            background: #fff;
            padding: 5rem 4rem;
        }
        .terapias__inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        .terapia-card {
            border-radius: .8rem;
            overflow: hidden;
            height: 22rem;
            background: var(--color-accent);
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 2rem;
        }
        .terapia-card__content {
            background: var(--color-secondary);
            color: #fff;
            border-radius: .8rem;
            padding: 1.2rem 1.6rem;
        }
        .terapia-card__icon {
            font-size: 2rem;
            margin-bottom: .4rem;
        }
        .terapia-card__title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* ── SOBRE MÍ ── */
        .sobre-mi {
            padding: 6rem 4rem;
            background: var(--color-bg-alt);
        }
        .sobre-mi__inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 5rem;
            align-items: center;
        }
        .sobre-mi__img-wrap {
            position: relative;
        }
        .sobre-mi__img {
            width: 100%;
            max-height: 42rem;
            object-fit: cover;
            border-radius: 2rem;
            box-shadow: 0 12px 40px rgba(0,0,0,.12);
        }
        .sobre-mi__img-placeholder {
            width: 100%;
            height: 36rem;
            background: var(--color-accent);
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 7rem;
        }
        .sobre-mi__label {
            color: var(--color-primary);
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
            margin-bottom: .6rem;
        }
        .sobre-mi__title {
            font-size: 3rem;
            font-weight: 700;
            color: var(--color-secondary);
            margin-bottom: 1.6rem;
            line-height: 1.2;
        }
        .sobre-mi__text {
            font-size: 1.5rem;
            color: var(--color-text-light);
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        .sobre-mi__checks {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .8rem 2rem;
        }
        .sobre-mi__check {
            display: flex;
            align-items: center;
            gap: .8rem;
            font-size: 1.4rem;
            color: var(--color-text);
        }
        .sobre-mi__check i {
            color: var(--color-primary);
            font-size: 1.2rem;
        }
        .sobre-mi__btn {
            margin-top: 2.4rem;
        }

        /* ── SERVICIOS ── */
        .servicios {
            padding: 6rem 4rem;
            background: #fff;
        }
        .servicios__inner {
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-header {
            margin-bottom: 3.6rem;
        }
        .section-header__label {
            color: var(--color-primary);
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
            margin-bottom: .4rem;
        }
        .section-header__title {
            font-size: 3rem;
            font-weight: 700;
            color: var(--color-secondary);
        }
        .servicios__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(22rem, 1fr));
            gap: 2rem;
        }
        .servicio-card {
            background: var(--color-bg-alt);
            border-radius: 1.6rem;
            padding: 2.4rem;
            border-top: 3px solid var(--color-primary);
            transition: transform .2s, box-shadow .2s;
        }
        .servicio-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }
        .servicio-card__icon {
            width: 5rem;
            height: 5rem;
            background: #fff;
            border-radius: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 2.2rem;
            margin-bottom: 1.6rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .servicio-card__title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--color-secondary);
            margin-bottom: .6rem;
        }
        .servicio-card__text {
            font-size: 1.3rem;
            color: var(--color-text-light);
            line-height: 1.6;
        }

        /* ── CTA BANNER ── */
        .cta-banner {
            background: var(--color-primary);
            padding: 6rem 4rem;
            text-align: center;
            color: #fff;
        }
        .cta-banner__title {
            font-size: 3.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .cta-banner__subtitle {
            font-size: 1.6rem;
            opacity: .88;
            margin-bottom: 2.8rem;
        }
        .cta-banner__btn {
            background: #fff;
            color: var(--color-primary);
            padding: 1.2rem 3.2rem;
            border-radius: 10rem;
            font-size: 1.6rem;
            font-weight: 700;
            display: inline-block;
        }

        /* ── FOOTER ── */
        .footer {
            background: var(--color-secondary);
            color: rgba(255,255,255,.85);
            padding: 3rem 4rem;
            text-align: center;
            font-size: 1.3rem;
        }
        .footer__top {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1.6rem;
        }
        .footer__social {
            width: 3.6rem;
            height: 3.6rem;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="preview-badge">
        <i class="fa-solid fa-eye"></i> Vista previa · {{ $theme['name'] }}
    </div>

    <!-- NAV -->
    <nav class="nav">
        <div class="nav__left">
            <div class="nav__header">
                @if ($profile?->foto_path)
                    <img class="nav__logo-img" src="{{ asset('storage/' . $profile->foto_path) }}" alt="Logo">
                @else
                    <div class="nav__logo-placeholder"><i class="fa-solid fa-user-doctor"></i></div>
                @endif
                <div>
                    <div class="nav__name">{{ $profile?->nombre_completo ?? 'Psicóloga' }}</div>
                    <div class="nav__role">Psicología · Bienestar</div>
                </div>
            </div>
            <div class="nav__links">
                <a href="#">Inicio</a>
                <a href="#">Sobre mí</a>
                <a href="#">Servicios</a>
                <a href="#">Blog</a>
                <a href="#">Pide cita</a>
            </div>
        </div>
        <div class="nav__contact">
            @if ($profile?->telefono_publico)
                <div class="nav__contact-icon"><i class="fa-solid fa-phone-volume"></i></div>
            @endif
            <div class="nav__contact-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <button class="nav__cta-btn">Pedir cita</button>
        </div>
    </nav>

    <!-- HERO / BANNER -->
    <section class="banner">
        @if ($heroUrl)
            <img src="{{ $heroUrl }}" alt="" class="banner__shape1">
        @endif
        <div class="banner__inner">
            <div class="banner__content">
                <span class="banner__eyebrow">Psicología · Bienestar emocional</span>
                <h1 class="banner__title">
                    Estamos aquí para<br>
                    escuchar tus <span>problemas</span>
                </h1>
                <p class="banner__slogan">
                    {{ $profile?->slogan ?? 'Tu bienestar emocional es mi prioridad. Juntos encontramos el camino hacia una vida plena y equilibrada.' }}
                </p>
                <div class="banner__btns">
                    <a href="#" class="btn btn--primary">Pedir cita</a>
                    <a href="#" class="btn btn--outline">Sobre mí</a>
                </div>
                @if ($profile?->telefono_publico)
                    <div class="banner__contact">
                        <i class="fa-solid fa-phone-volume"></i>
                        <span>{{ $profile->telefono_publico }}</span>
                        <span>·</span>
                        <span>{{ $profile?->nombre_completo ?? 'Psicóloga' }}</span>
                    </div>
                @endif
            </div>
            <div class="banner__img-wrap">
                @if ($heroUrl)
                    <img src="{{ $heroUrl }}" alt="{{ $profile?->nombre_completo ?? 'Psicóloga' }}">
                @else
                    <div class="banner__img-placeholder">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- TIPOS DE TERAPIA -->
    <section class="terapias">
        <div class="terapias__inner">
            @php
                use App\Models\Terapia;
                $terapias = Terapia::where('activo', true)->orderBy('orden')->limit(3)->get();
                $terapiaDefaults = [
                    ['icon' => 'fa-solid fa-person', 'title' => 'Terapia individual'],
                    ['icon' => 'fa-solid fa-children', 'title' => 'Terapia de pareja'],
                    ['icon' => 'fa-solid fa-people-group', 'title' => 'Terapia de grupo'],
                ];
            @endphp
            @if ($terapias->count())
                @foreach ($terapias as $terapia)
                    <div class="terapia-card">
                        <div class="terapia-card__content">
                            <div class="terapia-card__icon">
                                <i class="{{ $terapia->icono ?: 'fa-solid fa-brain' }}"></i>
                            </div>
                            <div class="terapia-card__title">{{ $terapia->titulo }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ($terapiaDefaults as $td)
                    <div class="terapia-card">
                        <div class="terapia-card__content">
                            <div class="terapia-card__icon"><i class="{{ $td['icon'] }}"></i></div>
                            <div class="terapia-card__title">{{ $td['title'] }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    <!-- SOBRE MÍ -->
    <section class="sobre-mi">
        <div class="sobre-mi__inner">
            <div class="sobre-mi__img-wrap">
                @if ($sobreMiUrl)
                    <img class="sobre-mi__img" src="{{ $sobreMiUrl }}" alt="{{ $profile?->nombre_completo ?? 'Psicóloga' }}">
                @elseif ($profile?->foto_path)
                    <img class="sobre-mi__img" src="{{ asset('storage/' . $profile->foto_path) }}" alt="{{ $profile?->nombre_completo }}">
                @else
                    <div class="sobre-mi__img-placeholder"><i class="fa-solid fa-user"></i></div>
                @endif
            </div>
            <div>
                <p class="sobre-mi__label">Bienvenida a la consulta</p>
                <h2 class="sobre-mi__title">Brindando terapias psicológicas de la mejor calidad.</h2>
                <p class="sobre-mi__text">
                    {{ $profile?->sobre_mi ? \Illuminate\Support\Str::limit($profile->sobre_mi, 280) : 'Soy psicóloga con amplia experiencia en el acompañamiento terapéutico. Mi objetivo es brindarte un espacio seguro donde puedas explorar tus emociones, superar dificultades y desarrollar tu bienestar.' }}
                </p>
                <div class="sobre-mi__checks">
                    <span class="sobre-mi__check"><i class="fa-solid fa-check"></i> Trastornos bipolares</span>
                    <span class="sobre-mi__check"><i class="fa-solid fa-check"></i> Manejo del estrés</span>
                    <span class="sobre-mi__check"><i class="fa-solid fa-check"></i> Terapia de depresión</span>
                    <span class="sobre-mi__check"><i class="fa-solid fa-check"></i> Terapia de ansiedad</span>
                    <span class="sobre-mi__check"><i class="fa-solid fa-check"></i> Terapia familiar</span>
                    <span class="sobre-mi__check"><i class="fa-solid fa-check"></i> Coaching ejecutivo</span>
                </div>
                <div class="sobre-mi__btn">
                    <a href="#" class="btn btn--primary">Pedir una cita</a>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section class="servicios">
        <div class="servicios__inner">
            <div class="section-header">
                <p class="section-header__label">Servicios que ofrecemos</p>
                <h2 class="section-header__title">¿En qué puedo ayudarte?</h2>
            </div>
            <div class="servicios__grid">
                @php
                    use App\Models\Servicio;
                    $servicios = Servicio::where('activo', true)->orderBy('orden')->limit(4)->get();
                @endphp
                @forelse ($servicios as $servicio)
                    <div class="servicio-card">
                        <div class="servicio-card__icon">
                            <i class="{{ $servicio->icono ?: 'fa-solid fa-heart' }}"></i>
                        </div>
                        <h3 class="servicio-card__title">{{ $servicio->titulo }}</h3>
                        @if ($servicio->descripcion)
                            <p class="servicio-card__text">{{ \Illuminate\Support\Str::limit($servicio->descripcion, 90) }}</p>
                        @endif
                    </div>
                @empty
                    @foreach ([
                        ['icon' => 'fa-solid fa-brain',          'title' => 'Ansiedad y estrés'],
                        ['icon' => 'fa-solid fa-heart',          'title' => 'Terapia individual'],
                        ['icon' => 'fa-solid fa-people-arrows',  'title' => 'Terapia de pareja'],
                        ['icon' => 'fa-solid fa-child-reaching', 'title' => 'Psicología infantil'],
                    ] as $s)
                        <div class="servicio-card">
                            <div class="servicio-card__icon"><i class="{{ $s['icon'] }}"></i></div>
                            <h3 class="servicio-card__title">{{ $s['title'] }}</h3>
                            <p class="servicio-card__text">Atención profesional y personalizada para cada persona.</p>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-banner">
        <h2 class="cta-banner__title">¿Listo para dar el primer paso?</h2>
        <p class="cta-banner__subtitle">
            Reserva una consulta y empieza tu camino hacia el bienestar.
            @if ($profile?->telefono_publico) &nbsp;·&nbsp; {{ $profile->telefono_publico }} @endif
        </p>
        <a href="#" class="cta-banner__btn">Pedir cita ahora</a>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer__top">
            <div class="footer__social"><i class="fa-brands fa-instagram"></i></div>
            <div class="footer__social"><i class="fa-brands fa-facebook"></i></div>
            <div class="footer__social"><i class="fa-brands fa-tiktok"></i></div>
            <div class="footer__social"><i class="fa-brands fa-whatsapp"></i></div>
        </div>
        <p>© {{ date('Y') }} · {{ $profile?->nombre_completo ?? 'Psicóloga' }} · Todos los derechos reservados</p>
    </footer>
</body>
</html>
