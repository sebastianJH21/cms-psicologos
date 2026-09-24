<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalación · PsicoCMS</title>
    <link rel="stylesheet" href="{{ asset('css/install/install.css') }}">
</head>
<body>
    <div class="wizard">
        <header class="wizard__header">
            <h1 class="wizard__brand">PsicoCMS</h1>
            <p class="wizard__subtitle">Asistente de instalación</p>
        </header>

        @include('install.partials.progress', ['step' => $step ?? 1, 'totalSteps' => $totalSteps ?? 6])

        <main class="wizard__main">
            @if (session('success'))
                <div class="alert alert--success" role="status">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert--error" role="alert">
                    <strong>Revisa los siguientes campos:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('paso')
        </main>

        <footer class="wizard__footer">
            <small>PsicoCMS · {{ now()->year }}</small>
        </footer>
    </div>

    <script src="{{ asset('js/install/install.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
