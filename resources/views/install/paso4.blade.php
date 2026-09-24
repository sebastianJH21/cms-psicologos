@extends('install.layout')

@section('paso')
    <section class="step">
        <header class="step__head">
            <h2 class="step__title">Paso 4 · Servicios, especialidades y planes</h2>
            <p class="step__desc">Añade los principales servicios que ofreces, tus especialidades y los planes con precios. Todo es opcional ahora; puedes editarlo después.</p>
        </header>

        <form method="POST" action="{{ route('install.step.process', ['n' => 4]) }}" class="form" novalidate>
            @csrf

            <fieldset class="block">
                <legend class="block__title">Servicios principales</legend>
                <p class="block__desc">Por ejemplo: terapia individual, terapia de pareja, evaluación psicológica…</p>
                <div class="repeater" data-repeater data-name="servicios">
                    <template data-repeater-template>
                        <div class="repeater__row">
                            <div class="field">
                                <label>Título</label>
                                <input type="text" data-name="titulo" maxlength="120">
                            </div>
                            <div class="field">
                                <label>Descripción breve</label>
                                <input type="text" data-name="descripcion" maxlength="600">
                            </div>
                            <button type="button" class="btn btn--danger btn--sm" data-repeater-remove>Eliminar</button>
                        </div>
                    </template>
                    <div class="repeater__list" data-repeater-list></div>
                    <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>+ Añadir servicio</button>
                </div>
            </fieldset>

            <fieldset class="block">
                <legend class="block__title">Especialidades</legend>
                <p class="block__desc">Por ejemplo: cognitivo-conductual, mindfulness, EMDR…</p>
                <div class="repeater" data-repeater data-name="terapias">
                    <template data-repeater-template>
                        <div class="repeater__row">
                            <div class="field">
                                <label>Título</label>
                                <input type="text" data-name="titulo" maxlength="120">
                            </div>
                            <div class="field">
                                <label>Descripción breve</label>
                                <input type="text" data-name="descripcion" maxlength="600">
                            </div>
                            <button type="button" class="btn btn--danger btn--sm" data-repeater-remove>Eliminar</button>
                        </div>
                    </template>
                    <div class="repeater__list" data-repeater-list></div>
                    <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>+ Añadir especialidad</button>
                </div>
            </fieldset>

            <fieldset class="block">
                <legend class="block__title">Planes y precios</legend>
                <p class="block__desc">Online y presencial. Indica precio y duración.</p>
                <div class="repeater" data-repeater data-name="planes">
                    <template data-repeater-template>
                        <div class="repeater__row repeater__row--plan">
                            <div class="field">
                                <label>Modalidad</label>
                                <select data-name="tipo">
                                    <option value="online">Online</option>
                                    <option value="presencial">Presencial</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Nombre</label>
                                <input type="text" data-name="nombre" maxlength="120">
                            </div>
                            <div class="field">
                                <label>Precio (€)</label>
                                <input type="number" data-name="precio" step="0.01" min="0">
                            </div>
                            <div class="field">
                                <label>Duración (min)</label>
                                <input type="number" data-name="duracion_min" min="5" max="600" value="60">
                            </div>
                            <div class="field field--full">
                                <label>Descripción</label>
                                <input type="text" data-name="descripcion" maxlength="600">
                            </div>
                            <button type="button" class="btn btn--danger btn--sm" data-repeater-remove>Eliminar</button>
                        </div>
                    </template>
                    <div class="repeater__list" data-repeater-list></div>
                    <button type="button" class="btn btn--ghost btn--sm" data-repeater-add>+ Añadir plan</button>
                </div>
            </fieldset>

            <div class="actions actions--between">
                <a class="btn btn--link" href="{{ route('install.step.show', ['n' => 3]) }}">← Volver</a>
                <button type="submit" class="btn btn--primary">Guardar y continuar</button>
            </div>
        </form>
    </section>
@endsection
