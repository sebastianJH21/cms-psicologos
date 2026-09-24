@php
    /** @var string $seccion */
    /** @var array $campos  Lista de ['key' => 'about_title', 'label' => 'Título', 'type' => 'input'|'textarea'] */
    $titulo = $titulo ?? 'Frases de esta sección';
    $descripcion = $descripcion ?? 'Personaliza las frases que aparecerán en esta sección de tu web pública.';
@endphp

<section class="frases-form panel">
    <header class="frases-form__head">
        <h2 class="frases-form__title">
            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
            {{ $titulo }}
        </h2>
        <p class="frases-form__sub">{{ $descripcion }}</p>
    </header>

    <form method="POST" action="{{ route('dashboard.frases.update', $seccion) }}" class="frases-form__form">
        @csrf
        @method('PUT')

        <div class="frases-form__grid">
            @foreach ($campos as $campo)
                @php
                    $esOverline = str_ends_with($campo['key'], '_overline');
                    $maxLen = $esOverline ? 28 : 300;
                @endphp
                <div class="form-field frases-form__field {{ ($campo['wide'] ?? false) ? 'frases-form__field--wide' : '' }}">
                    <label for="frase-{{ $seccion }}-{{ $campo['key'] }}">
                        <i class="fa-solid fa-{{ $campo['icon'] ?? 'quote-left' }}"></i>
                        {{ $campo['label'] }}
                    </label>
                    @if (($campo['type'] ?? 'input') === 'textarea')
                        <textarea
                            id="frase-{{ $seccion }}-{{ $campo['key'] }}"
                            name="{{ $campo['key'] }}"
                            rows="2"
                            maxlength="{{ $maxLen }}"
                        >{{ phrase($campo['key'], '') }}</textarea>
                    @else
                        <input
                            type="text"
                            id="frase-{{ $seccion }}-{{ $campo['key'] }}"
                            name="{{ $campo['key'] }}"
                            maxlength="{{ $maxLen }}"
                            value="{{ phrase($campo['key'], '') }}"
                        >
                    @endif
                    @if ($esOverline)
                        <small class="form-field__hint">Máximo 28 caracteres.</small>
                    @endif
                    @if (!empty($campo['hint']))
                        <small class="form-field__hint">{{ $campo['hint'] }}</small>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="form-actions form-actions--end">
            <button type="submit" class="btn btn--primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar frases
            </button>
        </div>
    </form>
</section>
