@php
    $servicios = $servicios ?? \App\Models\Servicio::where('activo', true)->orderBy('orden')->get();
@endphp
<section class="t-mod__section" id="services">
    <div class="t-mod__container">
        <div class="t-mod__sec-head">
            <span class="t-mod__overline">{{ phrase('servicios_overline', 'Servicios') }}</span>
            <h2 class="t-mod__h2">{{ phrase('servicios_title', 'Acompañamiento especializado') }}</h2>
            @if (phrase('servicios_description'))<p>{{ phrase('servicios_description') }}</p>@endif
        </div>
        @if ($servicios->isEmpty())
            <div class="t-mod__empty"><p>Pronto compartiré los servicios disponibles.</p></div>
        @else
            <div class="t-mod__services">
                @foreach ($servicios as $servicio)
                    <article class="t-mod__service" style="--i: {{ $loop->index }}">
                        <div class="t-mod__service-icon"><i class="fa-solid {{ $servicio->icono ?? 'fa-heart-pulse' }}"></i></div>
                        <h3>{{ $servicio->titulo }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($servicio->descripcion ?? ''), 120) }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
