@php
    $terapias = $terapias ?? \App\Models\Terapia::where('activo', true)->orderBy('orden')->get();
@endphp
@if ($terapias->isNotEmpty())
<section class="t-min__section">
    <div class="t-min__container">
        <p class="t-min__overline">— {{ phrase('especialidades_overline', 'Terapias') }}</p>
        <h2 class="t-min__h2">{{ phrase('especialidades_title', 'Enfoques disponibles') }}</h2>
        <div class="t-min__therapies">
            @foreach ($terapias as $terapia)
                <div class="t-min__therapy">
                    <h3>{{ $terapia->titulo }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 180) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
