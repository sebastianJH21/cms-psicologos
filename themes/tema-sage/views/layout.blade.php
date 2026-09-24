<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', $user?->nombre . ' ' . $user?->apellidos . ' · ' . ($profile?->slogan ?? 'Psicología clínica'))</title>
    <meta name="description" content="@yield('descripcion', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    <meta name="theme-color" content="#2980b9">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', $user?->nombre . ' ' . $user?->apellidos)">
    <meta property="og:description" content="@yield('og_description', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    @if ($profile?->foto_path)
        <meta property="og:image" content="{{ asset('storage/' . $profile->foto_path) }}">
    @endif

    @include('_shared.favicon')

    <link rel="stylesheet" href="{{ theme_asset('assets/css/fonts.css', 'tema-base') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wysiwyg.css') }}">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "{{ $user?->nombre }} {{ $user?->apellidos }}",
        "telephone": "{{ $profile?->telefono_publico }}",
        "email": "{{ $profile?->email_publico }}",
        @if ($profile?->direccion)"address": "{{ $profile->direccion }}",@endif
        "image": "{{ $profile?->foto_path ? asset('storage/' . $profile->foto_path) : '' }}",
        "description": "{{ \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 200) }}"
    }
    </script>
</head>
<body class="t-sage">
    @include('theme::partials.nav')

    <main class="t-sage__main">
        @yield('contenido')
    </main>

    @include('theme::partials.footer')

    <button class="t-sage__top" id="t-sage-top" aria-label="Volver arriba"><i class="fa-solid fa-chevron-up"></i></button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const top = document.getElementById('t-sage-top');
            const nav = document.querySelector('.t-sage__nav');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 400) top.classList.add('t-sage__top--show');
                else top.classList.remove('t-sage__top--show');
                if (nav && window.scrollY > 80) nav.classList.add('t-sage__nav--scrolled');
                else if (nav) nav.classList.remove('t-sage__nav--scrolled');
            });
            top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

            const toggle = document.querySelector('.t-sage__nav-toggle');
            const menu = document.querySelector('.t-sage__nav-list');
            if (toggle && menu) {
                toggle.addEventListener('click', () => menu.classList.toggle('t-sage__nav-list--open'));
            }
        });
    </script>
    <script src="{{ theme_asset('assets/js/blog-ajax.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
