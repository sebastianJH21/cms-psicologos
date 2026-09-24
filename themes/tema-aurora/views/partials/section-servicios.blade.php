@php
    $servicios = $servicios ?? \App\Models\Servicio::where('activo', true)->orderBy('orden')->get();
@endphp
<section class="t-aurora__services" id="services">
    <div class="t-aurora__container">
        <header class="t-aurora__sec-head">
            <span class="t-aurora__overline">{{ phrase('servicios_overline', '— Servicios') }}</span>
            <h2 class="t-aurora__h2">{{ phrase('servicios_title', 'Cómo puedo acompañarte') }}</h2>
            @if (phrase('servicios_description'))
                <p>{{ phrase('servicios_description') }}</p>
            @endif
        </header>

        @if ($servicios->isEmpty())
            <p class="t-aurora__empty">Pronto compartiré los servicios disponibles.</p>
        @else
            <div class="t-aurora__services-grid">
                @foreach ($servicios as $servicio)
                    <article class="t-aurora__service" style="--i: {{ $loop->index }}">
                        <span class="t-aurora__service-num">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="t-aurora__service-icon"><i class="fa-solid {{ $servicio->icono ?? 'fa-heart' }}"></i></div>
                        <h3>{{ $servicio->titulo }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($servicio->descripcion ?? ''), 130) }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
