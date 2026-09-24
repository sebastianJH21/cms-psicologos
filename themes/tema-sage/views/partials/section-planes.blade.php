@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-sage__section t-sage__section--alt" id="planes">
    <div class="t-sage__container">
        <div class="t-sage__section-header">
            <span class="t-sage__overtitle">{{ phrase('planes_overline', 'Tarifas') }}</span>
            <h2 class="t-sage__h2">{{ phrase('planes_title', 'Planes y precios') }}</h2>
        </div>
        <div class="t-sage__plans">
            @foreach ($planes as $plan)
                <div class="t-sage__plan">
                    <span class="t-sage__plan-tipo">{{ ucfirst($plan->tipo) }}</span>
                    <h3>{{ $plan->nombre }}</h3>
                    <div class="t-sage__plan-price">
                        <span class="t-sage__plan-amount">{{ number_format($plan->precio, 0) }}€</span>
                        <span class="t-sage__plan-duration">/ {{ $plan->duracion_min }} min</span>
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
