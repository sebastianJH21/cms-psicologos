@php
    $historia = $historia ?? null;
@endphp

<div class="historia-form__grid">
    <div class="form-field historia-form__field">
        <label for="fecha_sesion">
            <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
            Fecha de la sesión *
        </label>
        <input type="date" id="fecha_sesion" name="fecha_sesion" required
            value="{{ old('fecha_sesion', $historia?->fecha_sesion?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
        @error('fecha_sesion')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field historia-form__field">
        <label for="titulo">
            <i class="fa-solid fa-tag" aria-hidden="true"></i>
            Título de la sesión
        </label>
        <input type="text" id="titulo" name="titulo" maxlength="255"
            placeholder="Ej: Primera consulta, Sesión de seguimiento..."
            value="{{ old('titulo', $historia?->titulo) }}">
        <small class="form-field__hint">Opcional. Si no lo rellenas se usará la fecha como título.</small>
        @error('titulo')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field historia-form__field historia-form__field--full">
    <label for="contenido">
        <i class="fa-solid fa-pen-nib" aria-hidden="true"></i>
        Notas de la sesión *
    </label>
    <textarea id="contenido" name="contenido" data-jodit data-jodit-no-image rows="18">{{ old('contenido', $historia?->contenido) }}</textarea>
    @error('contenido')<small class="form-field__error">{{ $message }}</small>@enderror
</div>

<fieldset class="historia-form__fieldset historia-form__field">
    <legend>
        <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
        Adjuntar archivos
    </legend>
    <p class="historia-form__fieldset-hint">
        Puedes adjuntar fotos o documentos PDF escaneados con las anotaciones de la sesión.
        Máximo 10 archivos &middot; JPG, PNG, WebP, GIF o PDF &middot; Máx. 10 MB por archivo.
    </p>
    <div class="form-field" style="margin:0">
        <input type="file" id="archivos" name="archivos[]" multiple
            accept="image/jpeg,image/png,image/gif,image/webp,application/pdf">
        @error('archivos')<small class="form-field__error">{{ $message }}</small>@enderror
        @error('archivos.*')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</fieldset>

@include('dashboard.blog.partials.editor-jodit')
