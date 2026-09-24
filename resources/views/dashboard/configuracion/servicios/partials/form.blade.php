@php
    $servicio = $servicio ?? null;
@endphp

<div class="config-form__grid">
    <div class="form-field">
        <label for="titulo">
            <i class="fa-solid fa-tag" aria-hidden="true"></i>
            Título *
        </label>
        <input type="text" id="titulo" name="titulo" maxlength="150" required
            value="{{ old('titulo', $servicio?->titulo) }}">
        @error('titulo')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="icono">
            <i class="fa-solid fa-icons" aria-hidden="true"></i>
            Icono (FontAwesome)
        </label>
        <div class="icon-input">
            <span class="icon-input__preview" id="preview-icono">
                <i class="{{ old('icono', $servicio?->icono) ?: 'fa-solid fa-icons' }}" aria-hidden="true"></i>
            </span>
            <input type="text" id="icono" name="icono" maxlength="80"
                value="{{ old('icono', $servicio?->icono) }}"
                placeholder="fa-solid fa-heart"
                data-icon-preview="preview-icono">
            <button type="button" class="btn btn--ghost" data-icon-picker-trigger="icono">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                Elegir
            </button>
        </div>
        <small class="form-field__hint">Haz clic en "Elegir" para abrir el selector visual.</small>
        @error('icono')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="orden">
            <i class="fa-solid fa-arrow-up-1-9" aria-hidden="true"></i>
            Orden
        </label>
        <input type="number" id="orden" name="orden" min="0" value="{{ old('orden', $servicio?->orden) }}">
        <small class="form-field__hint">Cuanto menor el número, más arriba aparece.</small>
        @error('orden')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field">
    <label for="descripcion">
        <i class="fa-solid fa-align-left" aria-hidden="true"></i>
        Descripción
    </label>
    <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000">{{ old('descripcion', $servicio?->descripcion) }}</textarea>
    @error('descripcion')<small class="form-field__error">{{ $message }}</small>@enderror
</div>

<div class="form-field form-field--switch">
    <label class="switch">
        <input type="checkbox" name="activo" value="1" {{ old('activo', $servicio?->activo ?? true) ? 'checked' : '' }}>
        <span class="switch__slider"></span>
        <span class="switch__label">Visible en la web pública</span>
    </label>
</div>
