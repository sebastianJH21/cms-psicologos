<section class="t-bold__section t-bold__section--alt" id="about">
    <div class="t-bold__container">
        <div class="t-bold__about">
            <div class="t-bold__about-image">
                <img src="{{ theme_image('sobre-mi') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/sobre-mi.jpg')) }}" alt="Sobre mí">
                <div class="t-bold__about-experience">
                    {{ phrase('about_pie_foto', '+10 años acompañando') }}
                </div>
            </div>
            <div class="t-bold__about-content">
                <span class="t-bold__overtitle">{{ phrase('about_overline', 'Sobre mí') }}</span>
                <h2 class="t-bold__h2">{{ phrase('about_title', 'Una mirada profesional, un trato humano') }}</h2>
                @if ($profile?->sobre_mi)
                    <div class="t-bold__about-text wysiwyg">{!! $profile->sobre_mi !!}</div>
                @else
                    <p class="t-bold__about-text">Mi enfoque combina terapia cognitivo-conductual con escucha activa. Cada persona es única y cada proceso terapéutico requiere un acompañamiento personalizado.</p>
                @endif
                <ul class="t-bold__about-list">
                    <li><i class="fa-solid fa-circle-check"></i> Formación continua especializada</li>
                    <li><i class="fa-solid fa-circle-check"></i> Sesiones individuales, pareja y grupales</li>
                    <li><i class="fa-solid fa-circle-check"></i> Modalidad online o presencial</li>
                </ul>
            </div>
        </div>
    </div>
</section>
