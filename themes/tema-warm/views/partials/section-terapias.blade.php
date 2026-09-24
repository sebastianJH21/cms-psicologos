@php
    $terapias = $terapias ?? \App\Models\Terapia::where('activo', true)->orderBy('orden')->get();
@endphp
@if ($terapias->isNotEmpty())
<section class="t-warm__section">
    <div class="t-warm__container">
        <div class="t-warm__sec-head">
            <span class="t-warm__overline">— {{ phrase('especialidades_overline', 'Especialidades') }}</span>
            <h2 class="t-warm__h2">{{ phrase('especialidades_title', 'Acompañamientos disponibles') }}</h2>
            @if (phrase('especialidades_description'))<p>{{ phrase('especialidades_description') }}</p>@endif
        </div>
        <div class="t-warm__therapies">
            @foreach ($terapias as $terapia)
                <div class="t-warm__therapy">
                    <h3>{{ $terapia->titulo }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 140) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
