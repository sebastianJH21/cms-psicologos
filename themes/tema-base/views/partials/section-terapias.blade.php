@if (isset($terapias) && $terapias->isNotEmpty())
<div class="layout__therapies">
    <div class="therapies__container">
        @foreach ($terapias->take(3) as $i => $terapia)
            <div class="therapies__therapy {{ $i === 1 ? 'therapy-2' : ($i === 2 ? 'therapy-3' : '') }}">
                <div class="therapy__container-ico">
                    <i class="fa-solid {{ $terapia->icono ?? 'fa-person' }} therapy__ico-person"></i>
                </div>
                <div class="therapy__content">
                    <p class="therapy__title">{{ $terapia->titulo }}</p>
                    <p class="therapy__description">{{ \Illuminate\Support\Str::limit(strip_tags($terapia->descripcion ?? ''), 100) }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
