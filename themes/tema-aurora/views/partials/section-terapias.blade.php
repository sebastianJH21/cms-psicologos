@php
    $terapias = $terapias ?? \App\Models\Terapia::where('activo', true)->orderBy('orden')->get();
@endphp
@if ($terapias->isNotEmpty())
<section class="t-aurora__terapias">
    <div class="t-aurora__container">
        <header class="t-aurora__sec-head t-aurora__sec-head--left">
            <span class="t-aurora__overline">{{ phrase('especialidades_overline', '— Especialidades') }}</span>
            <h2 class="t-aurora__h2">{{ phrase('especialidades_title', 'Áreas en las que trabajo') }}</h2>
            @if (phrase('especialidades_description'))<p>{{ phrase('especialidades_description') }}</p>@endif
        </header>
        <div class="t-aurora__terapias-list">
            @foreach ($terapias as $terapia)
                <article class="t-aurora__terapia">
                    <div class="t-aurora__terapia-side"><i class="fa-solid {{ $terapia->icono ?: 'fa-leaf' }}"></i></div>
                    <div>
                        <h3>{{ $terapia->titulo }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 160) }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
