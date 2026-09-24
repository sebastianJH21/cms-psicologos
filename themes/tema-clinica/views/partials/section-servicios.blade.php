@php
    $servicios = $servicios ?? \App\Models\Servicio::where('activo', true)->orderBy('orden')->get();
@endphp
<section class="t-clinica__section" id="services">
    <div class="t-clinica__container">
        <div class="t-clinica__section-header">
            <span class="t-clinica__overtitle">{{ phrase('servicios_overline', 'Servicios') }}</span>
            <h2 class="t-clinica__h2">{{ phrase('servicios_title', '¿En qué puedo ayudarte?') }}</h2>
        </div>

        @if ($servicios->isEmpty())
            <div class="t-clinica__empty">
                <i class="fa-regular fa-folder-open"></i>
                <p>Pronto publicaré los servicios disponibles.</p>
            </div>
        @else
            <div class="t-clinica__services-grid">
                @foreach ($servicios as $servicio)
                    <article class="t-clinica__service">
                        <div class="t-clinica__service-icon">
                            <i class="fa-solid {{ $servicio->icono ?? 'fa-heart-pulse' }}"></i>
                        </div>
                        <h3>{{ $servicio->titulo }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($servicio->descripcion ?? ''), 130) }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
