@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-mod__section" id="planes">
    <div class="t-mod__container">
        <div class="t-mod__sec-head">
            <span class="t-mod__overline">{{ phrase('planes_overline', 'Tarifas') }}</span>
            <h2 class="t-mod__h2">{{ phrase('planes_title', 'Planes y precios') }}</h2>
            @if (phrase('planes_description'))<p>{{ phrase('planes_description') }}</p>@endif
        </div>
        <div class="t-mod__plans">
            @foreach ($planes as $plan)
                <div class="t-mod__plan {{ $loop->index === 1 ? 'is-featured' : '' }}">
                    @if ($loop->index === 1)<span class="t-mod__plan-badge">Más solicitado</span>@endif
                    <span class="t-mod__plan-tipo">{{ ucfirst($plan->tipo) }}</span>
                    <h3>{{ $plan->nombre }}</h3>
                    <div class="t-mod__plan-price"><strong>{{ number_format($plan->precio, 0) }}€</strong><span>/ {{ $plan->duracion_min }} min</span></div>
                    @if ($plan->descripcion)<p>{{ \Illuminate\Support\Str::limit(strip_tags($plan->descripcion), 120) }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
