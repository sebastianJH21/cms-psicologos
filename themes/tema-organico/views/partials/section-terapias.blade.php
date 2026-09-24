@php
    $terapias = $terapias ?? \App\Models\Terapia::where('activo', true)->orderBy('orden')->get();
@endphp
@if ($terapias->isNotEmpty())
<section class="t-organico__section" id="terapias">
    <div class="t-organico__container">
        <div class="t-organico__section-header">
            <span class="t-organico__overtitle">{{ phrase('especialidades_overline', 'Tipos de terapia') }}</span>
            <h2 class="t-organico__h2">{{ phrase('especialidades_title', 'Elige el enfoque que necesitas') }}</h2>
        </div>
        <div class="t-organico__therapies">
            @foreach ($terapias as $terapia)
                <div class="t-organico__therapy">
                    <div class="t-organico__therapy-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3>{{ $terapia->titulo }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 150) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
