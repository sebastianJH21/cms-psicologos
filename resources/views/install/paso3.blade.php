@extends('install.layout')

@section('paso')
    <section class="step">
        <header class="step__head">
            <h2 class="step__title">Paso 3 · Datos públicos</h2>
            <p class="step__desc">Información que verán los pacientes en tu web. Podrás modificar y ampliar todo desde el panel más adelante.</p>
        </header>

        <form method="POST" action="{{ route('install.step.process', ['n' => 3]) }}" class="form" novalidate>
            @csrf

            <div class="grid grid--1">
                <div class="field">
                    <label for="slogan">Frase gancho o eslogan</label>
                    <input type="text" id="slogan" name="slogan" value="{{ old('slogan') }}" maxlength="200" placeholder="Acompañamiento psicológico cercano y profesional">
                </div>
            </div>

            <div class="grid grid--2">
                <div class="field">
                    <label for="telefono_publico">Teléfono para citas</label>
                    <input type="tel" id="telefono_publico" name="telefono_publico" value="{{ old('telefono_publico') }}" required>
                </div>

                <div class="field">
                    <label for="email_publico">Email para citas</label>
                    <input type="email" id="email_publico" name="email_publico" value="{{ old('email_publico') }}" required>
                </div>
            </div>

            <div class="grid grid--1">
                <div class="field">
                    <label for="numero_colegiado">Número de colegiado <span class="field__hint">(opcional)</span></label>
                    <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado') }}" maxlength="50" placeholder="Ej. M-12345">
                </div>
            </div>

            <div class="grid grid--1">
                <div class="field">
                    <label for="sobre_mi">Sobre mí</label>
                    <textarea id="sobre_mi" name="sobre_mi" rows="6" maxlength="5000" placeholder="Cuéntales a tus pacientes quién eres, tu enfoque y tu experiencia.">{{ old('sobre_mi') }}</textarea>
                </div>

                <div class="field">
                    <label for="direccion">Dirección de la consulta</label>
                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}" maxlength="255" placeholder="Calle, número, ciudad">
                </div>
            </div>

            <div class="actions actions--between">
                <a class="btn btn--link" href="{{ route('install.step.show', ['n' => 2]) }}">← Volver</a>
                <button type="submit" class="btn btn--primary">Guardar y continuar</button>
            </div>
        </form>
    </section>
@endsection
