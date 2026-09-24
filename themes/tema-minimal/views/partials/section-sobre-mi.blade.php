<section class="t-min__section t-min__section--alt" id="about">
    <div class="t-min__container t-min__container--narrow">
        <p class="t-min__overline">— {{ phrase('about_overline', 'Sobre mí') }}</p>
        <h2 class="t-min__h2">{{ phrase('about_title', 'Tu proceso terapéutico, paso a paso') }}</h2>
        @if ($profile?->sobre_mi)
            <div class="t-min__about-text wysiwyg">{!! $profile->sobre_mi !!}</div>
        @else
            <p class="t-min__about-text">Mi enfoque parte de la escucha activa y el respeto. Cada persona necesita su propio ritmo, y mi trabajo es facilitar ese proceso con herramientas basadas en evidencia.</p>
        @endif
    </div>
</section>
