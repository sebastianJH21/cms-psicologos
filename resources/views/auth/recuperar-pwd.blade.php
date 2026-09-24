@extends('auth.layout')

@section('titulo', 'Recuperar contraseña')

@section('contenido')
<div class="auth-container">
    <div class="auth-box">
        <h1 class="auth-title">Recuperar contraseña</h1>
        <p class="auth-subtitle">Indica tu email y teléfono para resetear tu contraseña</p>

        @if ($errors->has('error'))
            <div class="auth-error">{{ $errors->first('error') }}</div>
        @endif

        <form method="POST" action="{{ route('recuperar-pwd.recover') }}" novalidate class="auth-form">
            @csrf

            <div class="form__field">
                <label class="form__label" for="email">Email <span aria-hidden="true">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form__input @error('email') form__input--error @enderror"
                    value="{{ old('email') }}"
                    required
                    placeholder="tu@email.com"
                >
                @error('email')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form__field">
                <label class="form__label" for="telefono">Teléfono <span aria-hidden="true">*</span></label>
                <input
                    type="tel"
                    id="telefono"
                    name="telefono"
                    class="form__input @error('telefono') form__input--error @enderror"
                    value="{{ old('telefono') }}"
                    required
                    placeholder="+34 600 000 000"
                >
                @error('telefono')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form__field">
                <label class="form__label" for="nueva_contrasena">Nueva contraseña <span aria-hidden="true">*</span></label>
                <input
                    type="password"
                    id="nueva_contrasena"
                    name="nueva_contrasena"
                    class="form__input @error('nueva_contrasena') form__input--error @enderror"
                    required
                    placeholder="••••••••"
                >
                @error('nueva_contrasena')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form__field">
                <label class="form__label" for="nueva_contrasena_confirmation">Confirmar contraseña <span aria-hidden="true">*</span></label>
                <input
                    type="password"
                    id="nueva_contrasena_confirmation"
                    name="nueva_contrasena_confirmation"
                    class="form__input @error('nueva_contrasena_confirmation') form__input--error @enderror"
                    required
                    placeholder="••••••••"
                >
                @error('nueva_contrasena_confirmation')
                    <span class="form__error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn--primary btn--block">Actualizar contraseña</button>
        </form>

        <div class="auth-footer">
            <p>¿Recuerdas tu contraseña? <a href="{{ route('login') }}" class="auth-link">Iniciar sesión</a></p>
        </div>
    </div>
</div>
@endsection
