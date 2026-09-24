@if (session('success'))
    <div class="flash flash--success" role="status" data-flash>
        <i class="fa-solid fa-circle-check flash__icon" aria-hidden="true"></i>
        <span class="flash__text">{{ session('success') }}</span>
        <button type="button" class="flash__close" data-flash-close aria-label="Cerrar">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="flash flash--error" role="alert" data-flash>
        <i class="fa-solid fa-circle-exclamation flash__icon" aria-hidden="true"></i>
        <span class="flash__text">{{ session('error') }}</span>
        <button type="button" class="flash__close" data-flash-close aria-label="Cerrar">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
@endif

@if (session('info'))
    <div class="flash flash--info" role="status" data-flash>
        <i class="fa-solid fa-circle-info flash__icon" aria-hidden="true"></i>
        <span class="flash__text">{{ session('info') }}</span>
        <button type="button" class="flash__close" data-flash-close aria-label="Cerrar">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
@endif
