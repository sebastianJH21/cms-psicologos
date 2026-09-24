@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard/icon-picker.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('js/dashboard/icon-picker.js') }}" defer></script>
    @endpush
@endonce

{{-- Modal selector de iconos FontAwesome --}}
<div class="modal" id="modal-icon-picker" hidden aria-modal="true" role="dialog" aria-labelledby="modal-icon-picker-titulo">
    <div class="modal__backdrop" data-icon-picker-close></div>
    <div class="modal__dialog modal__dialog--wide">
        <div class="modal__header">
            <h2 class="modal__title" id="modal-icon-picker-titulo">
                <i class="fa-solid fa-icons" aria-hidden="true"></i>
                Selecciona un icono
            </h2>
            <button type="button" class="modal__close" data-icon-picker-close aria-label="Cerrar">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="modal__body">
            <input type="text" id="icon-picker-search" class="icon-picker__search"
                placeholder="Buscar icono... (ej: heart, brain, user)" autocomplete="off">
            <div class="icon-picker__grid" id="icon-picker-grid"></div>
        </div>
    </div>
</div>
