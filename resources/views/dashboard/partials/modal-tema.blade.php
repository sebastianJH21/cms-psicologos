@php
    $themeColors = [
        ['color' => '#2c4a7e', 'name' => 'Azul profesional'],
        ['color' => '#1a6b5a', 'name' => 'Verde bienestar'],
        ['color' => '#7c3d8c', 'name' => 'Púrpura serenidad'],
        ['color' => '#b05e3e', 'name' => 'Terracota cálido'],
        ['color' => '#2e7da8', 'name' => 'Azul cielo'],
        ['color' => '#c0687e', 'name' => 'Rosa empático'],
        ['color' => '#5a6e4a', 'name' => 'Verde naturaleza'],
        ['color' => '#4e4b8a', 'name' => 'Índigo meditación'],
        ['color' => '#e85d75', 'name' => 'Rosa coral vitalidad'],
        ['color' => '#17b8a0', 'name' => 'Turquesa tranquilidad'],
        ['color' => '#d4a574', 'name' => 'Dorado calidez'],
    ];
    $currentUser = auth()->user();
    $currentMode = $currentUser->theme_preference ?? 'light';
    $currentColor = $currentUser->primary_color ?? '#2c4a7e';
@endphp

<div
    class="modal-overlay modal-tema"
    id="modal-tema-dashboard"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-tema-title"
    hidden
>
    <div class="modal-tema__inner">
        <div class="modal-tema__header">
            <h2 class="modal-tema__title" id="modal-tema-title">
                <i class="fa-solid fa-palette" aria-hidden="true"></i>
                Apariencia del panel
            </h2>
            <button type="button" class="btn btn--icon" id="modal-tema-close" aria-label="Cerrar">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <form
            id="form-tema-dashboard"
            action="{{ route('dashboard.preferencias.tema') }}"
            method="POST"
        >
            @csrf

            <p class="modal-tema__section-title">Modo</p>
            <div class="tema-mode-btns">
                <div class="tema-mode-btn">
                    <input
                        type="radio"
                        name="theme_mode"
                        id="mode-light"
                        value="light"
                        @checked($currentMode === 'light')
                    >
                    <label class="tema-mode-btn__label" for="mode-light">
                        <i class="fa-solid fa-sun tema-mode-btn__icon" aria-hidden="true"></i>
                        <span class="tema-mode-btn__text">Claro</span>
                    </label>
                </div>
                <div class="tema-mode-btn">
                    <input
                        type="radio"
                        name="theme_mode"
                        id="mode-dark"
                        value="dark"
                        @checked($currentMode === 'dark')
                    >
                    <label class="tema-mode-btn__label" for="mode-dark">
                        <i class="fa-solid fa-moon tema-mode-btn__icon" aria-hidden="true"></i>
                        <span class="tema-mode-btn__text">Oscuro</span>
                    </label>
                </div>
            </div>

            <p class="modal-tema__section-title">Color principal</p>
            <div class="color-swatches" role="radiogroup" aria-label="Color principal">
                @foreach ($themeColors as $tc)
                    <button
                        type="button"
                        class="color-swatch {{ $currentColor === $tc['color'] ? 'color-swatch--selected' : '' }}"
                        data-color="{{ $tc['color'] }}"
                        title="{{ $tc['name'] }}"
                        aria-label="{{ $tc['name'] }}"
                        style="background: {{ $tc['color'] }}"
                    ></button>
                @endforeach
            </div>
            <input type="hidden" name="primary_color" id="input-primary-color" value="{{ $currentColor }}">

            <div class="modal-tema__footer">
                <button type="button" class="btn btn--ghost" id="modal-tema-close-footer">Cancelar</button>
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Guardar apariencia
                </button>
            </div>
        </form>
    </div>
</div>
