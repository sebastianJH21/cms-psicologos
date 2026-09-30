<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', $user?->nombre . ' ' . $user?->apellidos . ' · ' . ($profile?->slogan ?? 'Psicología'))</title>
    <meta name="description" content="@yield('descripcion', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    <meta name="theme-color" content="#6b4ee6">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', $user?->nombre . ' ' . $user?->apellidos)">
    <meta property="og:description" content="@yield('og_description', \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 160))">
    @if ($profile?->foto_path)
        <meta property="og:image" content="{{ asset('storage/' . $profile->foto_path) }}">
    @endif

    @include('_shared.canonical')

    @include('_shared.favicon')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,600;9..144,700&family=Inter:wght@300;400;500;600;700&display=swap">
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
        "description": "{{ \Illuminate\Support\Str::limit(strip_tags($profile?->sobre_mi ?? ''), 200) }}"
    }
    </script>
</head>
<body class="t-aurora">
    <div class="t-aurora__aurora" aria-hidden="true">
        <span class="t-aurora__blob t-aurora__blob--1"></span>
        <span class="t-aurora__blob t-aurora__blob--2"></span>
        <span class="t-aurora__blob t-aurora__blob--3"></span>
    </div>

    @include('theme::partials.nav')

    <main class="t-aurora__main">
        @yield('contenido')
    </main>

    @include('theme::partials.footer')

    <button type="button" class="t-aurora__top" id="t-aurora-top" aria-label="Volver arriba">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <script src="{{ theme_asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
