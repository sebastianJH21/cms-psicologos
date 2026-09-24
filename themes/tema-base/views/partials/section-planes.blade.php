@if (isset($planes) && $planes->isNotEmpty())
<div class="layout__prices" id="prices">
    <div class="prices__container">
        <header class="prices__header">
            <h4 class="prices__subtitle">{{ phrase('planes_overline', 'Planes y precios') }}</h4>
            <h2 class="prices__title">{{ phrase('planes_title', 'Elige la opción que mejor te encaje') }}</h2>
            @if (phrase('planes_description'))
                <p class="prices__description" style="text-align:center; max-width:60rem; margin: 1.2rem auto 0;">{{ phrase('planes_description') }}</p>
            @endif
        </header>

        <div class="prices__list-prices">
            @foreach ($planes as $plan)
                <div class="list-prices__price">
                    <div class="price__content">
                        <div class="price__container-ico">
                            <i class="fa-solid {{ $plan->tipo === 'online' ? 'fa-globe' : 'fa-user' }} container-ico__ico"></i>
                        </div>

                        <h2 class="price__online">{{ number_format($plan->precio, 0, ',', '.') }}€</h2>
                        <p class="price__text">{{ $plan->nombre }} · {{ ucfirst($plan->tipo) }}</p>

                        @if ($plan->descripcion)
                            <ul class="price__list-details">
                                @foreach (explode("\n", $plan->descripcion) as $linea)
                                    @if (trim($linea))
                                        <li class="list-details__item">
                                            <i class="fa-solid fa-circle"></i>
                                            <span>{{ trim($linea) }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        <p class="price__text" style="margin-top: 1rem; font-size: 1.4rem;">{{ $plan->duracion_min }} min/sesión</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
