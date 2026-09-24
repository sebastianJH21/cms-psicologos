@if ($features['sobre_mi'] ?? true)
@php
    $isLanding = ($themeMode ?? 'landing') === 'landing';
    $telefonoLimpio = preg_replace('/[^+0-9]/', '', $profile?->telefono_publico ?? '');
    $citaUrl = ($features['reservas'] ?? true)
        ? ($isLanding ? '#cita' : url('/pide-cita'))
        : ($telefonoLimpio ? 'tel:' . $telefonoLimpio : '#');
@endphp

<div class="layout__characteristics" id="about">
    <div class="characteristics__container">
        <div class="characteristics__details">
            <h3 class="details__subtitle">{{ phrase('about_overline', 'Bienvenida a la consulta') }}</h3>
            <h2 class="details__title">{{ phrase('about_title', $profile?->slogan ?: 'Brindando terapias psicológicas de la mejor calidad.') }}</h2>

            @if ($profile?->sobre_mi)
                <div class="details__description wysiwyg">
                    {!! $profile->sobre_mi !!}
                </div>
            @endif

            @if (isset($terapias) && $terapias->isNotEmpty())
                <ul class="details__list-services">
                    @foreach ($terapias->take(8) as $terapia)
                        <li class="list-services__item">
                            <i class="fa-solid fa-check list-services__ico"></i>
                            <p class="list-services__text">{{ $terapia->titulo }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a class="details__btn-appointment" href="{{ $citaUrl }}">{{ phrase('hero_cta', 'Pedir una cita') }}</a>
        </div>

        @php
            $aboutImgBase = theme_image('sobre-mi') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/psicologa.jpg'));
        @endphp
        <div class="characteristics__right">
            <div class="characteristics__container-img">
                <img class="characteristics__img" src="{{ $aboutImgBase }}" alt="{{ $user?->nombre }}">
            </div>
        </div>
    </div>
</div>
@endif
