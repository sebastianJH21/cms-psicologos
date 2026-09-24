@php
    $servicios = $servicios ?? \App\Models\Servicio::where('activo', true)->orderBy('orden')->get();
@endphp
<section class="t-min__section" id="services">
    <div class="t-min__container">
        <p class="t-min__overline">— {{ phrase('servicios_overline', 'Servicios') }}</p>
        <h2 class="t-min__h2">{{ phrase('servicios_title', 'Áreas de intervención') }}</h2>

        @if ($servicios->isEmpty())
            <p class="t-min__empty">Pronto compartiré los servicios disponibles.</p>
        @else
            <div class="t-min__list">
                @foreach ($servicios as $servicio)
                    <div class="t-min__list-item">
                        <span class="t-min__list-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3>{{ $servicio->titulo }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($servicio->descripcion ?? ''), 200) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
