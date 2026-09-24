@php
    $terapias = $terapias ?? \App\Models\Terapia::where('activo', true)->orderBy('orden')->get();
@endphp
@if ($terapias->isNotEmpty())
<section class="t-mod__section">
    <div class="t-mod__container">
        <div class="t-mod__sec-head">
            <span class="t-mod__overline">{{ phrase('especialidades_overline', 'Especialidades') }}</span>
            <h2 class="t-mod__h2">{{ phrase('especialidades_title', 'Enfoques terapéuticos') }}</h2>
            @if (phrase('especialidades_description'))<p>{{ phrase('especialidades_description') }}</p>@endif
        </div>
        <div class="t-mod__therapies">
            @foreach ($terapias as $terapia)
                <div class="t-mod__therapy">
                    <span class="t-mod__therapy-dot"></span>
                    <h3>{{ $terapia->titulo }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 140) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
