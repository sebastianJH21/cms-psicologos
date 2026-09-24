@extends('install.layout')

@section('paso')
    <section class="step">
        <header class="step__head">
            <h2 class="step__title">Paso 6 · Foto de perfil</h2>
            <p class="step__desc">Sube tu foto profesional para mostrarla en la web. <strong>Preferiblemente sin fondo</strong> (PNG transparente). Puedes saltarte este paso y subirla más tarde.</p>
        </header>

        <form method="POST" action="{{ route('install.step.process', ['n' => 6]) }}" class="form" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="upload">
                <label class="upload__zone" for="foto">
                    <span class="upload__icon" aria-hidden="true">📷</span>
                    <span class="upload__text">Selecciona una imagen (jpg, png o webp · máx. 4MB)</span>
                    <span class="upload__filename" id="upload-filename"></span>
                </label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" data-upload-input>
                <img id="upload-preview" class="upload__preview" alt="" hidden>
            </div>

            <div class="actions actions--between">
                <a class="btn btn--link" href="{{ route('install.step.show', ['n' => 5]) }}">← Volver</a>
                <button type="submit" class="btn btn--primary">Finalizar instalación</button>
            </div>
        </form>
    </section>
@endsection
