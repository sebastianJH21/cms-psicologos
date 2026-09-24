@php
    $planes = $planes ?? \App\Models\PlanPrecio::orderBy('orden')->get();
@endphp
@if ($planes->isNotEmpty())
<section class="t-aurora__planes" id="planes">
    <div class="t-aurora__container">
        <header class="t-aurora__sec-head">
            <span class="t-aurora__overline">{{ phrase('planes_overline', '— Tarifas') }}</span>
            <h2 class="t-aurora__h2">{{ phrase('planes_title', 'Planes transparentes') }}</h2>
            @if (phrase('planes_description'))<p>{{ phrase('planes_description') }}</p>@endif
        </header>
        <div class="t-aurora__planes-grid">
            @foreach ($planes as $plan)
                <article class="t-aurora__plan {{ $loop->index === 1 ? 't-aurora__plan--featured' : '' }}">
                    @if ($loop->index === 1)<span class="t-aurora__plan-tag">Recomendado</span>@endif
                    <span class="t-aurora__plan-tipo">{{ ucfirst($plan->tipo) }}</span>
                    <h3>{{ $plan->nombre }}</h3>
                    <div class="t-aurora__plan-precio">
                        <strong>{{ number_format($plan->precio, 0, ',', '.') }}€</strong>
                        <span>/ {{ $plan->duracion_min }} min</span>
                    </div>
                    @if ($plan->descripcion)
                        <ul class="t-aurora__plan-features">
                            @foreach (array_filter(array_map('trim', explode("\n", $plan->descripcion))) as $linea)
                                <li><i class="fa-solid fa-check"></i> {{ $linea }}</li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
