@extends('auth.layout')

@section('titulo', 'Acceso')
@section('subtitulo', 'Introduce tus datos para acceder al panel')

@section('contenido')
    <h2 id="auth-title" class="auth__title">Iniciar sesión</h2>

    @if ($errors->any())
        <div class="auth__alert" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="auth__form" novalidate>
        @csrf

        <div class="auth__field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
                inputmode="email"
                @class(['auth__input', 'auth__input--error' => $errors->has('email')])
            >
        </div>

        <div class="auth__field">
            <label for="telefono">Teléfono</label>
            <input
                type="tel"
                id="telefono"
                name="telefono"
                value="{{ old('telefono') }}"
                autocomplete="tel"
                required
                inputmode="tel"
                @class(['auth__input', 'auth__input--error' => $errors->has('telefono')])
            >
            <small class="auth__hint">El mismo número con el que te registraste.</small>
        </div>

        <div class="auth__field">
            <label for="password">Contraseña</label>
            <div class="auth__password">
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                    @class(['auth__input', 'auth__input--error' => $errors->has('password')])
                >
                <button type="button" class="auth__toggle" data-toggle-password aria-label="Mostrar u ocultar contraseña" aria-pressed="false">
                    <span data-icon-show>Mostrar</span>
                    <span data-icon-hide hidden>Ocultar</span>
                </button>
            </div>
        </div>

        <label class="auth__remember">
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            <span>Recordarme en este dispositivo</span>
        </label>

        <button type="submit" class="auth__submit">Acceder al panel</button>
    </form>

    <div class="auth__footer" style="margin-top: 2rem; text-align: center;">
        @if (app()->isLocal())
            <p style="font-size: 1.3rem;">¿Olvidaste la contraseña? <a href="{{ route('recuperar-pwd.show') }}" style="color: var(--color-primary); text-decoration: none;">Recuperarla aquí</a></p>
        @else
            <p style="font-size: 1.3rem;">¿Olvidaste la contraseña? <a href="https://victorroblesweb.es/contacto" target="_blank" rel="noopener" style="color: var(--color-primary); text-decoration: none;">Contactar con soporte</a></p>
        @endif
    </div>
@endsection
