@php
    $aboutImg = theme_image('sobre-mi') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/sobre-mi.jpg'));
@endphp
<section class="t-warm__section t-warm__section--alt" id="about">
    <div class="t-warm__container">
        <div class="t-warm__about">
            <div class="t-warm__about-image">
                <img src="{{ $aboutImg }}" alt="Sobre mí">
            </div>
            <div class="t-warm__about-content">
                <span class="t-warm__overline">— {{ phrase('about_overline', 'Sobre mí') }}</span>
                <h2 class="t-warm__h2">{{ phrase('about_title', 'Mi enfoque') }}</h2>
                @if ($profile?->sobre_mi)
                    <div class="t-warm__about-text wysiwyg">{!! $profile->sobre_mi !!}</div>
                @else
                    <p class="t-warm__about-text">Creo en la psicología cercana, libre de juicios y centrada en lo que cada persona necesita.</p>
                @endif
                <ul class="t-warm__about-list">
                    <li><i class="fa-solid fa-heart"></i> Escucha activa y respetuosa</li>
                    <li><i class="fa-solid fa-heart"></i> Espacio seguro y confidencial</li>
                    <li><i class="fa-solid fa-heart"></i> Tratamiento personalizado</li>
                </ul>
            </div>
        </div>
    </div>
</section>
