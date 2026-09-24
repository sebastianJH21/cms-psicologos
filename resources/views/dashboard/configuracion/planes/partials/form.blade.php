@php
    $plan = $plan ?? null;
@endphp

<div class="config-form__grid">
    <div class="form-field">
        <label for="tipo">
            <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
            Tipo *
        </label>
        <select id="tipo" name="tipo" required>
            <option value="online" {{ old('tipo', $plan?->tipo) === 'online' ? 'selected' : '' }}>Online</option>
            <option value="presencial" {{ old('tipo', $plan?->tipo) === 'presencial' ? 'selected' : '' }}>Presencial</option>
        </select>
        @error('tipo')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="nombre">
            <i class="fa-solid fa-tag" aria-hidden="true"></i>
            Nombre del plan *
        </label>
        <input type="text" id="nombre" name="nombre" maxlength="150" required
            value="{{ old('nombre', $plan?->nombre) }}" placeholder="Ej: Sesión individual">
        @error('nombre')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="precio">
            <i class="fa-solid fa-euro-sign" aria-hidden="true"></i>
            Precio (€) *
        </label>
        <input type="number" step="0.01" min="0" id="precio" name="precio" required
            value="{{ old('precio', $plan?->precio) }}">
        @error('precio')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="duracion_min">
            <i class="fa-solid fa-clock" aria-hidden="true"></i>
            Duración (min) *
        </label>
        <input type="number" min="5" max="600" id="duracion_min" name="duracion_min" required
            value="{{ old('duracion_min', $plan?->duracion_min ?? 60) }}">
        @error('duracion_min')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="orden">
            <i class="fa-solid fa-arrow-up-1-9" aria-hidden="true"></i>
            Orden
        </label>
        <input type="number" id="orden" name="orden" min="0" value="{{ old('orden', $plan?->orden) }}">
        @error('orden')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field">
    <label for="descripcion">
        <i class="fa-solid fa-align-left" aria-hidden="true"></i>
        Descripción
    </label>
    <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000">{{ old('descripcion', $plan?->descripcion) }}</textarea>
    @error('descripcion')<small class="form-field__error">{{ $message }}</small>@enderror
</div>
