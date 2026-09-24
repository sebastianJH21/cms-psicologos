@php
    $terapias = $terapias ?? \App\Models\Terapia::where('activo', true)->orderBy('orden')->get();
@endphp
@if ($terapias->isNotEmpty())
<section class="t-clinica__section" id="terapias">
    <div class="t-clinica__container">
        <div class="t-clinica__section-header">
            <span class="t-clinica__overtitle">{{ phrase('especialidades_overline', 'Tipos de terapia') }}</span>
            <h2 class="t-clinica__h2">{{ phrase('especialidades_title', 'Elige el enfoque que necesitas') }}</h2>
        </div>
        <div class="t-clinica__therapies">
            @foreach ($terapias as $terapia)
                <div class="t-clinica__therapy">
                    <div class="t-clinica__therapy-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3>{{ $terapia->titulo }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 150) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
