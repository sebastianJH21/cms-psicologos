<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('titulo', 'Acceso') · PsicoCMS</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
    <main class="auth">
        <section class="auth__card" aria-labelledby="auth-title">
            <header class="auth__header">
                <h1 class="auth__brand">PsicoCMS</h1>
                <p class="auth__subtitle">@yield('subtitulo', 'Panel privado de la psicóloga')</p>
            </header>

            @yield('contenido')
        </section>

        <footer class="auth__footer">
            <small>PsicoCMS · {{ now()->year }}</small>
        </footer>
    </main>

    <script src="{{ asset('js/auth/login.js') }}" defer></script>
</body>
</html>
