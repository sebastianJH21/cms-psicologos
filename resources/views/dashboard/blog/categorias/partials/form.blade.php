@php
    $categoria = $categoria ?? null;
@endphp

<div class="cita-form__grid">
    <div class="form-field">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required value="{{ old('nombre', $categoria?->nombre) }}">
        @error('nombre')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="slug">Slug (opcional)</label>
        <input type="text" id="slug" name="slug" maxlength="120" value="{{ old('slug', $categoria?->slug) }}" placeholder="se generará automáticamente">
        <small class="form-field__hint">Solo letras minúsculas, números y guiones.</small>
        @error('slug')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="orden">Orden</label>
        <input type="number" id="orden" name="orden" min="0" value="{{ old('orden', $categoria?->orden ?? '') }}">
        @error('orden')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field">
    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="3" maxlength="300">{{ old('descripcion', $categoria?->descripcion) }}</textarea>
    @error('descripcion')<small class="form-field__error">{{ $message }}</small>@enderror
</div>
