@extends('dashboard.layout')

@section('titulo', 'Email y notificaciones')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Configuración'],
        ['label' => 'Email y notificaciones'],
    ];
@endphp

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-envelope-circle-check" aria-hidden="true"></i>
                Email y notificaciones
            </h1>
            <p class="page-header__subtitle">
                Configura el correo para recibir avisos cuando un paciente reserve una cita.
            </p>
        </div>
    </header>

    <div class="panel email-tutorial">
        <h2 class="email-tutorial__title">
            <i class="fa-brands fa-google" aria-hidden="true"></i>
            ¿Cómo configurar Gmail para enviar emails?
        </h2>
        <ol class="email-tutorial__steps">
            <li>Inicia sesión en tu cuenta de Gmail en <strong>myaccount.google.com</strong>.</li>
            <li>Ve a <strong>Seguridad</strong> → <strong>Verificación en dos pasos</strong> y actívala si no lo está.</li>
            <li>Vuelve a <strong>Seguridad</strong> → busca <strong>Contraseñas de aplicación</strong> (solo aparece si tienes 2FA activado).</li>
            <li>Selecciona "Correo" y "Otro (nombre personalizado)" → escribe "PsicoCMS" → clic en <strong>Generar</strong>.</li>
            <li>Copia la contraseña de 16 caracteres generada y pégala en el campo "Contraseña de aplicación" abajo.</li>
        </ol>
        <p class="email-tutorial__note">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            El servidor SMTP de Gmail es <strong>smtp.gmail.com</strong>, puerto <strong>587</strong>.
            El email de usuario y el remitente son tu dirección de Gmail.
        </p>
    </div>

    <form method="POST" action="{{ route('dashboard.configuracion.email-notificaciones.update') }}">
        @csrf
        @method('PUT')

        <section class="panel">
            <h2 class="panel__title">
                <i class="fa-solid fa-server" aria-hidden="true"></i>
                Configuración SMTP
            </h2>

            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="smtp_host">Servidor SMTP</label>
                    <input
                        type="text"
                        id="smtp_host"
                        name="smtp_host"
                        class="form-input"
                        value="{{ old('smtp_host', $config['smtp_host']) }}"
                        placeholder="smtp.gmail.com"
                        autocomplete="off"
                    >
                    @error('smtp_host') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="smtp_port">Puerto SMTP</label>
                    <input
                        type="number"
                        id="smtp_port"
                        name="smtp_port"
                        class="form-input"
                        value="{{ old('smtp_port', $config['smtp_port']) }}"
                        placeholder="587"
                        min="1"
                        max="65535"
                    >
                    @error('smtp_port') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="smtp_user">Email de Gmail</label>
                    <input
                        type="email"
                        id="smtp_user"
                        name="smtp_user"
                        class="form-input"
                        value="{{ old('smtp_user', $config['smtp_user']) }}"
                        placeholder="tuemail@gmail.com"
                        autocomplete="email"
                    >
                    @error('smtp_user') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="smtp_password">
                        Contraseña de aplicación Gmail
                        @if ($config['has_password'])
                            <span class="badge badge--success" style="font-size:1.1rem">Guardada</span>
                        @endif
                    </label>
                    <input
                        type="password"
                        id="smtp_password"
                        name="smtp_password"
                        class="form-input"
                        placeholder="{{ $config['has_password'] ? '••••••••••••••••' : 'Contraseña de 16 caracteres de Google' }}"
                        autocomplete="new-password"
                    >
                    <span class="form-hint">Deja en blanco para no cambiar la contraseña guardada.</span>
                    @error('smtp_password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="from_address">Email remitente (para mostrar)</label>
                    <input
                        type="email"
                        id="from_address"
                        name="from_address"
                        class="form-input"
                        value="{{ old('from_address', $config['from_address']) }}"
                        placeholder="tuemail@gmail.com"
                        autocomplete="off"
                    >
                    @error('from_address') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-field" style="margin-top: 2rem">
                <label class="checkbox-field">
                    <input type="hidden" name="notif_enabled" value="0">
                    <input
                        type="checkbox"
                        name="notif_enabled"
                        value="1"
                        @checked($config['notif_enabled'])
                    >
                    <span class="checkbox-field__label">
                        <i class="fa-solid fa-bell" aria-hidden="true"></i>
                        Activar notificaciones por email al recibir nuevas reservas
                    </span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar configuración
                </button>
            </div>
        </section>
    </form>
@endsection
