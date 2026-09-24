@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-min__section t-min__section--alt" id="planes">
    <div class="t-min__container">
        <p class="t-min__overline">— {{ phrase('planes_overline', 'Tarifas') }}</p>
        <h2 class="t-min__h2">{{ phrase('planes_title', 'Planes y precios') }}</h2>
        <div class="t-min__plans">
            @foreach ($planes as $plan)
                <div class="t-min__plan">
                    <p class="t-min__plan-tipo">{{ ucfirst($plan->tipo) }}</p>
                    <h3>{{ $plan->nombre }}</h3>
                    <p class="t-min__plan-price"><strong>{{ number_format($plan->precio, 0) }}€</strong> · {{ $plan->duracion_min }} min</p>
                    @if ($plan->descripcion)<p class="t-min__plan-desc">{{ \Illuminate\Support\Str::limit(strip_tags($plan->descripcion), 130) }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
