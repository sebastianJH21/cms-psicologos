@php
    $articulo = $articulo ?? null;
    $publishedValue = old('published_at', optional($articulo?->published_at)->format('Y-m-d\TH:i'));
@endphp

<div class="cita-form__grid">
    <div class="form-field form-field--full">
        <label for="titulo">Título *</label>
        <input type="text" id="titulo" name="titulo" maxlength="200" required value="{{ old('titulo', $articulo?->titulo) }}">
        @error('titulo')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="slug">Slug (opcional)</label>
        <input type="text" id="slug" name="slug" maxlength="220" value="{{ old('slug', $articulo?->slug) }}" placeholder="se generará automáticamente">
        <small class="form-field__hint">Solo letras minúsculas, números y guiones.</small>
        @error('slug')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id">
            <option value="">— Sin categoría —</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}" {{ (string) old('categoria_id', $articulo?->categoria_id) === (string) $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="estado">Estado *</label>
        <select id="estado" name="estado" required>
            @foreach ($estados as $key => $label)
                <option value="{{ $key }}" {{ old('estado', $articulo?->estado ?? 'borrador') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('estado')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="published_at">Fecha de publicación</label>
        <input type="datetime-local" id="published_at" name="published_at" value="{{ $publishedValue }}">
        <small class="form-field__hint">Obligatoria si el estado es "publicado".</small>
        @error('published_at')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field">
    <label for="extracto">Extracto</label>
    <textarea id="extracto" name="extracto" rows="2" maxlength="300">{{ old('extracto', $articulo?->extracto) }}</textarea>
    <small class="form-field__hint">Resumen breve para el listado del blog (máx. 300 caracteres).</small>
    @error('extracto')<small class="form-field__error">{{ $message }}</small>@enderror
</div>

<div class="form-field">
    <label for="contenido">Contenido *</label>
    <textarea id="contenido" name="contenido" data-jodit data-jodit-no-image rows="15">{{ old('contenido', $articulo?->contenido) }}</textarea>
    @error('contenido')<small class="form-field__error">{{ $message }}</small>@enderror
</div>

<fieldset class="blog-form__fieldset">
    <legend>Imagen destacada</legend>

    @if ($articulo?->imagen_path)
        <div class="blog-form__imagen-actual">
            <img src="{{ $articulo->imagen_url }}" alt="Imagen actual">
            <label class="blog-form__check">
                <input type="checkbox" name="eliminar_imagen" value="1">
                Eliminar imagen actual
            </label>
        </div>
    @endif

    <div class="form-field">
        <label for="imagen">Subir imagen</label>
        <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small class="form-field__hint">JPG, PNG o WebP. Máx. 4 MB.</small>
        @error('imagen')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</fieldset>

<fieldset class="blog-form__fieldset">
    <legend>SEO (opcional)</legend>

    <div class="form-field">
        <label for="meta_title">Meta título</label>
        <input type="text" id="meta_title" name="meta_title" maxlength="200" value="{{ old('meta_title', $articulo?->meta_title) }}">
        @error('meta_title')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="meta_description">Meta descripción</label>
        <textarea id="meta_description" name="meta_description" rows="2" maxlength="300">{{ old('meta_description', $articulo?->meta_description) }}</textarea>
        @error('meta_description')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</fieldset>

@include('dashboard.blog.partials.editor-jodit')
