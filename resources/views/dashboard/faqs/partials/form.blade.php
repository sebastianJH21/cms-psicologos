@php
    $faq = $faq ?? null;
@endphp

<div class="faq-form__grid">
    <div class="form-field">
        <label for="pregunta">
            <i class="fa-solid fa-circle-question" aria-hidden="true"></i>
            Pregunta *
        </label>
        <input type="text" id="pregunta" name="pregunta" maxlength="300" required
            value="{{ old('pregunta', $faq?->pregunta) }}">
        @error('pregunta')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>

    <div class="form-field">
        <label for="respuesta">
            <i class="fa-solid fa-message" aria-hidden="true"></i>
            Respuesta *
        </label>
        <textarea id="respuesta" name="respuesta" rows="6" maxlength="5000" required>{{ old('respuesta', $faq?->respuesta) }}</textarea>
        <small class="form-field__hint">Texto plano. Sé claro y conciso.</small>
        @error('respuesta')<small class="form-field__error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-field faq-form__switch">
    <label class="switch">
        <input type="checkbox" name="activa" value="1" {{ old('activa', $faq?->activa ?? true) ? 'checked' : '' }}>
        <span class="switch__slider"></span>
        <span class="switch__label">Visible en la web pública</span>
    </label>
</div>
