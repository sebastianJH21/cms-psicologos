@if (($features['servicios'] ?? true) && isset($servicios) && $servicios->isNotEmpty())
<div class="layout__services" id="services">
    <div class="services__container">
        <div class="services__header">
            <div class="services__title-box">
                <h3 class="services__subtitle">{{ phrase('servicios_overline', 'Servicios que ofrezco') }}</h3>
                <h2 class="services__title">{{ phrase('servicios_title', 'Terapia para personas que necesitan ayuda') }}</h2>
            </div>
            <p class="services__description">{{ phrase('servicios_description', 'Selecciona el servicio que mejor encaje con lo que estás buscando. Si tienes dudas, escríbeme y lo hablamos.') }}</p>
        </div>

        <div class="services__grid">
            @foreach ($servicios as $servicio)
                <div class="services__service-card">
                    <div class="service-card__ico-box">
                        <i class="fa-solid {{ $servicio->icono ?: 'fa-heart' }} service-card__ico"></i>
                    </div>
                    <div class="service-card__body">
                        <h3 class="service-card__title">{{ $servicio->titulo }}</h3>
                        <p class="service-card__description">{{ \Illuminate\Support\Str::limit(strip_tags($servicio->descripcion ?? ''), 140) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
