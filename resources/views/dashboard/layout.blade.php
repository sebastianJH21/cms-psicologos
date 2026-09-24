<!DOCTYPE html>
@php
    $authUser = auth()->user();
    $themeMode = $authUser->theme_preference ?? 'light';
    $primaryColor = $authUser->primary_color ?? '#2c4a7e';
@endphp
<html lang="es" data-theme="{{ $themeMode }}" data-primary-color="{{ $primaryColor }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="{{ $primaryColor }}">
    @include('_shared.favicon')
    <title>@yield('titulo', 'Panel') · PsicoCMS</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/configuracion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/dark-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wysiwyg.css') }}">
    <style>:root { --color-primary: {{ $primaryColor }}; --color-primary-dark: {{ $primaryColor }}; --color-primary-soft: {{ $primaryColor }}1a; --color-accent: {{ $primaryColor }}; }</style>
    @stack('styles')
</head>
<body class="dashboard">
    <a class="dashboard__skip" href="#main-content">Saltar al contenido</a>

    @include('dashboard.partials.sidebar')

    <div class="dashboard__main">
        @include('dashboard.partials.header')

        <main id="main-content" class="dashboard__content" tabindex="-1">
            @include('dashboard.partials.breadcrumbs')
            @include('dashboard.partials.flash')

            @yield('contenido')
        </main>

        @include('dashboard.partials.footer')
    </div>

    @include('dashboard.partials.modal-confirm')
    @include('dashboard.partials.modal-tema')

    <script src="{{ asset('js/dashboard/sidebar.js') }}" defer></script>
    <script src="{{ asset('js/dashboard/modal.js') }}" defer></script>
    <script src="{{ asset('js/dashboard/flash.js') }}" defer></script>
    <script src="{{ asset('js/dashboard/theme-toggle.js') }}" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset("sw.js") }}').then(reg => {
                reg.unregister();
            }).catch(() => {});
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
