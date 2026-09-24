@php
    $servicios = $servicios ?? \App\Models\Servicio::where('activo', true)->orderBy('orden')->get();
@endphp
<section class="t-warm__section" id="services">
    <div class="t-warm__container">
        <div class="t-warm__sec-head">
            <span class="t-warm__overline">— {{ phrase('servicios_overline', 'Servicios') }}</span>
            <h2 class="t-warm__h2">{{ phrase('servicios_title', 'Cómo puedo acompañarte') }}</h2>
            @if (phrase('servicios_description'))
                <p>{{ phrase('servicios_description') }}</p>
            @endif
        </div>
        @if ($servicios->isEmpty())
            <div class="t-warm__empty"><p>Pronto compartiré los servicios.</p></div>
        @else
            <div class="t-warm__services">
                @foreach ($servicios as $servicio)
                    <div class="t-warm__service">
                        <div class="t-warm__service-icon"><i class="fa-solid {{ $servicio->icono ?? 'fa-heart' }}"></i></div>
                        <h3>{{ $servicio->titulo }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($servicio->descripcion ?? ''), 130) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
