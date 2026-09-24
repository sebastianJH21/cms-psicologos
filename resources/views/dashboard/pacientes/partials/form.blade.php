@php
    $paciente = $paciente ?? null;
@endphp

<fieldset class="paciente-form__fieldset">
    <legend>Datos personales</legend>

    <div class="cita-form__grid">
        <div class="form-field">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" maxlength="100" required value="{{ old('nombre', $paciente?->nombre) }}">
            @error('nombre')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>

        <div class="form-field">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" maxlength="150" value="{{ old('apellidos', $paciente?->apellidos) }}">
            @error('apellidos')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>

        <div class="form-field">
            <label for="dni">DNI / NIE</label>
            <input type="text" id="dni" name="dni" maxlength="20" value="{{ old('dni', $paciente?->dni) }}" placeholder="12345678A">
            @error('dni')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>

        <div class="form-field">
            <label for="telefono">Teléfono *</label>
            <input type="tel" id="telefono" name="telefono" maxlength="30" required value="{{ old('telefono', $paciente?->telefono) }}" placeholder="+34 600 000 000">
            <small class="form-field__hint">Identificador único. Se normalizará automáticamente.</small>
            @error('telefono')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>

        <div class="form-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="150" value="{{ old('email', $paciente?->email) }}">
            @error('email')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>

        <div class="form-field">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $paciente?->fecha_nacimiento?->format('Y-m-d')) }}">
            @error('fecha_nacimiento')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>

        <div class="form-field">
            <label for="genero">Género</label>
            <select id="genero" name="genero">
                <option value="">Sin especificar</option>
                @foreach ($generos as $key => $label)
                    <option value="{{ $key }}" {{ old('genero', $paciente?->genero) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('genero')<small class="form-field__error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="form-field">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" maxlength="255" value="{{ old('direccion', $paciente?->direccion) }}">
        @error('direccion')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</fieldset>

<fieldset class="paciente-form__fieldset">
    <legend>Información clínica</legend>

    <div class="form-field">
        <label for="motivo_inicial">Motivo inicial de consulta</label>
        <textarea id="motivo_inicial" name="motivo_inicial" rows="3" maxlength="2000">{{ old('motivo_inicial', $paciente?->motivo_inicial) }}</textarea>
        @error('motivo_inicial')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="notas">Notas internas</label>
        <textarea id="notas" name="notas" rows="4" maxlength="5000">{{ old('notas', $paciente?->notas) }}</textarea>
        <small class="form-field__hint">Solo visibles para ti.</small>
        @error('notas')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</fieldset>
