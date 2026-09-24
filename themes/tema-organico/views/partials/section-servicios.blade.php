@php
    $servicios = $servicios ?? \App\Models\Servicio::where('activo', true)->orderBy('orden')->get();
@endphp
<section class="t-organico__section" id="services">
    <div class="t-organico__container">
        <div class="t-organico__section-header">
            <span class="t-organico__overtitle">{{ phrase('servicios_overline', 'Servicios') }}</span>
            <h2 class="t-organico__h2">{{ phrase('servicios_title', '¿En qué puedo ayudarte?') }}</h2>
        </div>

        @if ($servicios->isEmpty())
            <div class="t-organico__empty">
                <i class="fa-regular fa-folder-open"></i>
                <p>Pronto publicaré los servicios disponibles.</p>
            </div>
        @else
            <div class="t-organico__services-grid">
                @foreach ($servicios as $servicio)
                    <article class="t-organico__service">
                        <div class="t-organico__service-icon">
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
