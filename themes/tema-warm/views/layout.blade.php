<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', $user?->nombre . ' ' . $user?->apellidos . ' · ' . ($profile?->slogan ?? 'Psicología'))</title>
    <meta name="description" content="@yield('descripcion', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    <meta name="theme-color" content="#c0687e">

    @include('_shared.canonical')

    @include('_shared.favicon')

    <link rel="stylesheet" href="{{ theme_asset('assets/css/fonts.css', 'tema-base') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wysiwyg.css') }}">
</head>
<body class="t-warm">
    @include('theme::partials.nav')

    <main class="t-warm__main">
        @yield('contenido')
    </main>

    @include('theme::partials.footer')

    <button class="t-warm__top" id="t-warm-top" aria-label="Arriba"><i class="fa-solid fa-arrow-up"></i></button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const top = document.getElementById('t-warm-top');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 400) top.classList.add('is-show');
                else top.classList.remove('is-show');
            });
            top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

            const toggle = document.querySelector('.t-warm__nav-toggle');
            const menu = document.querySelector('.t-warm__nav-list');
            if (toggle && menu) toggle.addEventListener('click', () => menu.classList.toggle('is-open'));
        });
    </script>
    <script src="{{ theme_asset('assets/js/blog-ajax.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
