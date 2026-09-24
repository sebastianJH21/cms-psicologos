<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('titulo', $user?->nombre . ' ' . $user?->apellidos . ' · ' . ($profile?->slogan ?? 'Psicología y bienestar'))</title>
    <meta name="description" content="@yield('descripcion', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    <meta name="theme-color" content="#976147">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', $user?->nombre . ' ' . $user?->apellidos)">
    <meta property="og:description" content="@yield('og_description', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    @if ($profile?->foto_path)
        <meta property="og:image" content="{{ asset('storage/' . $profile->foto_path) }}">
    @endif

    @include('_shared.favicon')

    <link rel="stylesheet" href="{{ theme_asset('assets/css/fonts.css', 'tema-base') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset('assets/css/reset.css', 'tema-base') }}">
    <link rel="stylesheet" href="{{ theme_asset('assets/css/styles.css', 'tema-base') }}">
    @php $activeThemeSlug = app(\App\Services\ThemeManager::class)->activeSlug(); @endphp
    @if ($activeThemeSlug !== 'tema-base' && file_exists(base_path("themes/{$activeThemeSlug}/assets/css/theme.css")))
        <link rel="stylesheet" href="{{ theme_asset('assets/css/theme.css', $activeThemeSlug) }}">
    @endif
    <link rel="stylesheet" href="{{ theme_asset('assets/css/responsive.css', 'tema-base') }}">
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

<body class="{{ ($themeMode ?? 'landing') === 'multipage' ? 'mode-multipage' : 'mode-landing' }}">
    <div class="layout">
        <div class="layout__background"></div>

        <div class="layout__container-banner">
            @include('theme::partials.nav')
            @include('theme::partials.nav-mobile')

            @yield('hero')
        </div>

        @yield('contenido')

        @include('theme::partials.footer')
    </div>

    @include('theme::partials.scroll-top')

    <script src="{{ theme_asset('assets/js/main.js', 'tema-base') }}"></script>
    <script src="{{ theme_asset('assets/js/navMobile.js', 'tema-base') }}"></script>
    <script src="{{ theme_asset('assets/js/banner.js', 'tema-base') }}"></script>
    <script src="{{ theme_asset('assets/js/scroll-top.js', 'tema-base') }}"></script>
    <script src="{{ theme_asset('assets/js/video.js', 'tema-base') }}"></script>
    <script src="{{ theme_asset('assets/js/navFixed.js', 'tema-base') }}"></script>
    <script src="{{ theme_asset('assets/js/blog-ajax.js', 'tema-base') }}" defer></script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(regs => {
                regs.forEach(r => r.unregister());
            });
            if (window.caches) {
                caches.keys().then(keys => keys.forEach(k => caches.delete(k)));
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
