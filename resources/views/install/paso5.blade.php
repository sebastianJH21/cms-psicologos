@extends('install.layout')

@section('paso')
    @php
        $temas = [
            'tema-base' => ['nombre' => 'Tema base', 'desc' => 'Cálido, profesional y completo.'],
            'tema-minimal' => ['nombre' => 'Minimal', 'desc' => 'Limpio y minimalista, foco en la lectura.'],
            'tema-clinica' => ['nombre' => 'Clínica', 'desc' => 'Tonos serenos y aire profesional.'],
            'tema-warm' => ['nombre' => 'Warm', 'desc' => 'Acogedor con tonos terrosos y cálidos.'],
            'tema-modern' => ['nombre' => 'Modern', 'desc' => 'Tipografía moderna y maquetación amplia.'],
        ];
    @endphp

    <section class="step">
        <header class="step__head">
            <h2 class="step__title">Paso 5 · Tema visual</h2>
            <p class="step__desc">Elige uno de estos temas destacados y el formato de tu web. Tienes 11 temas disponibles en total: una vez instalado, podrás explorar y activar el resto desde el panel.</p>
        </header>

        <form method="POST" action="{{ route('install.step.process', ['n' => 5]) }}" class="form" novalidate>
            @csrf

            <div class="themes" role="radiogroup" aria-label="Temas destacados">
                @foreach ($temas as $slug => $info)
                    <label class="theme-card">
                        <input type="radio" name="tema" value="{{ $slug }}" @checked(old('tema', 'tema-base') === $slug) required>
                        <div class="theme-card__inner">
                            <div class="theme-card__preview theme-card__preview--{{ $slug }}"></div>
                            <h3 class="theme-card__name">{{ $info['nombre'] }}</h3>
                            <p class="theme-card__desc">{{ $info['desc'] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

            <p class="themes__more">Hay <strong>6 temas más</strong> esperándote en el panel (Aurora, Bold, Natural, Orgánico, Sage y Violeta). Podrás verlos y activarlos en cualquier momento desde <em>Gestión Web → Temas</em>.</p>

            <fieldset class="block block--row">
                <legend class="block__title">Formato de la web</legend>
                <label class="radio">
                    <input type="radio" name="modo" value="landing" @checked(old('modo', 'landing') === 'landing')>
                    <span><strong>Landing</strong> — toda la web en una sola página con scroll.</span>
                </label>
                <label class="radio">
                    <input type="radio" name="modo" value="multipage" @checked(old('modo') === 'multipage')>
                    <span><strong>Multipágina</strong> — secciones separadas con su propia URL.</span>
                </label>
            </fieldset>

            <div class="actions actions--between">
                <a class="btn btn--link" href="{{ route('install.step.show', ['n' => 4]) }}">← Volver</a>
                <button type="submit" class="btn btn--primary">Continuar</button>
            </div>
        </form>
    </section>
@endsection
