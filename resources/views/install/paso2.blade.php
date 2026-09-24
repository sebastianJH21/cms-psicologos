@extends('install.layout')

@section('paso')
    <section class="step">
        <header class="step__head">
            <h2 class="step__title">Paso 2 · Cuenta de acceso</h2>
            <p class="step__desc">Estos datos serán los que utilices para iniciar sesión en el panel de administración. Son privados y solo tuyos.</p>
        </header>

        <form method="POST" action="{{ route('install.step.process', ['n' => 2]) }}" class="form" novalidate>
            @csrf

            <div class="grid grid--2">
                <div class="field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required autocomplete="given-name">
                </div>

                <div class="field">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required autocomplete="family-name">
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div class="field">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" required autocomplete="tel">
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" minlength="10">
                    <small class="field__hint">Mínimo 10 caracteres, incluyendo al menos una mayúscula y un número.</small>
                </div>

                <div class="field">
                    <label for="password_confirmation">Repetir contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" minlength="10">
                </div>
            </div>

            <div class="install-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Guarda bien tu contraseña.</strong>
                    Una vez finalizada la instalación, solo podrás cambiarla desde el panel de administración en <em>Configuración → Mi perfil</em>. No existe recuperación automática por email.
                </div>
            </div>

            <div class="actions actions--end">
                <button type="submit" class="btn btn--primary">Guardar y continuar</button>
            </div>
        </form>
    </section>
@endsection
