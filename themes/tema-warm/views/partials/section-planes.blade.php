@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-warm__section t-warm__section--alt" id="planes">
    <div class="t-warm__container">
        <div class="t-warm__sec-head">
            <span class="t-warm__overline">— {{ phrase('planes_overline', 'Tarifas') }}</span>
            <h2 class="t-warm__h2">{{ phrase('planes_title', 'Planes y precios') }}</h2>
            @if (phrase('planes_description'))<p>{{ phrase('planes_description') }}</p>@endif
        </div>
        <div class="t-warm__plans">
            @foreach ($planes as $plan)
                <div class="t-warm__plan">
                    <span class="t-warm__plan-tipo">{{ ucfirst($plan->tipo) }}</span>
                    <h3>{{ $plan->nombre }}</h3>
                    <div class="t-warm__plan-price"><strong>{{ number_format($plan->precio, 0) }}€</strong> <span>/ {{ $plan->duracion_min }} min</span></div>
                    @if ($plan->descripcion)<p>{{ \Illuminate\Support\Str::limit(strip_tags($plan->descripcion), 130) }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
