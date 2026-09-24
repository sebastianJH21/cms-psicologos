@extends('dashboard.layout')

@section('titulo', 'Mi perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard/perfil-privado.css') }}">
@endpush

@php
    use Illuminate\Support\Facades\Storage;
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Mi perfil'],
    ];
    $avatarUrl = $usuario->avatar_path ? route('dashboard.perfil-privado.avatar') : null;
@endphp

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-user-circle" aria-hidden="true"></i>
                Mi perfil
            </h1>
            <p class="page-header__subtitle">
                Actualiza tus datos de acceso privados al panel de administración.
            </p>
        </div>
    </header>

    @if ($avatarUrl)
        <form
            method="POST"
            action="{{ route('dashboard.perfil-privado.avatar.destroy') }}"
            id="form-eliminar-avatar"
            data-confirm="¿Seguro que deseas eliminar tu avatar? Esta acción no se puede deshacer."
            data-confirm-title="Eliminar avatar"
            data-confirm-label="Eliminar"
            data-confirm-style="danger"
        >
            @csrf
            @method('DELETE')
        </form>
    @endif

    <form method="POST" action="{{ route('dashboard.perfil-privado.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="panel" style="margin-bottom: 2rem">
            <h2 class="panel__title">
                <i class="fa-solid fa-image-portrait" aria-hidden="true"></i>
                Avatar
            </h2>

            <div class="perfil-avatar-section">
                <div class="perfil-avatar-preview">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="perfil-avatar-img" id="avatar-preview">
                    @else
                        <div class="perfil-avatar-placeholder" id="avatar-preview-placeholder">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                        </div>
                        <img src="" alt="Avatar" class="perfil-avatar-img" id="avatar-preview" style="display:none">
                    @endif
                </div>

                <div class="perfil-avatar-actions">
                    <label class="btn btn--secondary" for="avatar" style="cursor:pointer">
                        <i class="fa-solid fa-upload" aria-hidden="true"></i>
                        Subir avatar
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none">
                    <span class="form-hint">JPG, PNG o WEBP. Máx. 2 MB.</span>
                    @error('avatar') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                @if ($avatarUrl)
                    <button
                        type="button"
                        class="btn btn--danger-outline"
                        style="margin-left:auto"
                        onclick="document.getElementById('form-eliminar-avatar').dispatchEvent(new Event('submit', {bubbles:true, cancelable:true}))"
                    >
                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                        Eliminar avatar
                    </button>
                @endif
            </div>
        </section>

        <section class="panel" style="margin-bottom: 2rem">
            <h2 class="panel__title">
                <i class="fa-solid fa-id-card" aria-hidden="true"></i>
                Datos personales
            </h2>

            <div class="perfil-form-grid">
                <div class="form-field">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="form-input"
                        value="{{ old('nombre', $usuario->nombre) }}"
                        required
                        maxlength="80"
                        autocomplete="given-name"
                    >
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="apellidos">Apellidos</label>
                    <input
                        type="text"
                        id="apellidos"
                        name="apellidos"
                        class="form-input"
                        value="{{ old('apellidos', $usuario->apellidos) }}"
                        maxlength="120"
                        autocomplete="family-name"
                    >
                    @error('apellidos') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="email">Email de acceso</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email', $usuario->email) }}"
                        required
                        maxlength="150"
                        autocomplete="email"
                    >
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="telefono">Teléfono de acceso</label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        class="form-input"
                        value="{{ old('telefono', $usuario->telefono) }}"
                        required
                        maxlength="30"
                        autocomplete="tel"
                    >
                    @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="panel" style="margin-bottom: 2rem">
            <h2 class="panel__title">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                Cambiar contraseña
            </h2>
            <p class="form-hint" style="margin-bottom:2rem">Deja los campos en blanco si no deseas cambiar la contraseña.</p>

            <div class="perfil-form-grid">
                <div class="form-field">
                    <label class="form-label" for="password">Nueva contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        autocomplete="new-password"
                        placeholder="Mínimo 10 caracteres, mayúsculas y números"
                    >
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        autocomplete="new-password"
                        placeholder="Repite la nueva contraseña"
                    >
                </div>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                Guardar cambios
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('avatar').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-preview-placeholder');
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };

        reader.readAsDataURL(file);
    });
</script>
@endpush
