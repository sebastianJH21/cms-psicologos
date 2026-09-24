@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-violeta__section t-violeta__section--alt" id="planes">
    <div class="t-violeta__container">
        <div class="t-violeta__section-header">
            <span class="t-violeta__overtitle">{{ phrase('planes_overline', 'Tarifas') }}</span>
            <h2 class="t-violeta__h2">{{ phrase('planes_title', 'Planes y precios') }}</h2>
        </div>
        <div class="t-violeta__plans">
            @foreach ($planes as $plan)
                <div class="t-violeta__plan">
                    <span class="t-violeta__plan-tipo">{{ ucfirst($plan->tipo) }}</span>
                    <h3>{{ $plan->nombre }}</h3>
                    <div class="t-violeta__plan-price">
                        <span class="t-violeta__plan-amount">{{ number_format($plan->precio, 0) }}€</span>
                        <span class="t-violeta__plan-duration">/ {{ $plan->duracion_min }} min</span>
                    </div>
                    @if ($plan->descripcion)
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($plan->descripcion), 130) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
