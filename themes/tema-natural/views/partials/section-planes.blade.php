@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-natural__section t-natural__section--alt" id="planes">
    <div class="t-natural__container">
        <div class="t-natural__section-header">
            <span class="t-natural__overtitle">{{ phrase('planes_overline', 'Tarifas') }}</span>
            <h2 class="t-natural__h2">{{ phrase('planes_title', 'Planes y precios') }}</h2>
        </div>
        <div class="t-natural__plans">
            @foreach ($planes as $plan)
                <div class="t-natural__plan">
                    <span class="t-natural__plan-tipo">{{ ucfirst($plan->tipo) }}</span>
                    <h3>{{ $plan->nombre }}</h3>
                    <div class="t-natural__plan-price">
                        <span class="t-natural__plan-amount">{{ number_format($plan->precio, 0) }}€</span>
                        <span class="t-natural__plan-duration">/ {{ $plan->duracion_min }} min</span>
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
