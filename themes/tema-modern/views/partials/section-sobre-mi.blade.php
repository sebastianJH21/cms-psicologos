<section class="t-mod__section" id="about">
    <div class="t-mod__container">
        <div class="t-mod__about">
            <div class="t-mod__about-image">
                <img src="{{ theme_image('sobre-mi') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : theme_asset('assets/img/sobre-mi.jpg')) }}" alt="Sobre mí">
            </div>
            <div class="t-mod__about-content">
                <span class="t-mod__overline">{{ phrase('about_overline', 'Sobre mí') }}</span>
                <h2 class="t-mod__h2">{{ phrase('about_title', 'Una mirada cercana y profesional') }}</h2>
                @if ($profile?->sobre_mi)
                    <div class="t-mod__about-text wysiwyg">{!! $profile->sobre_mi !!}</div>
                @else
                    <p class="t-mod__about-text">Mi enfoque combina técnicas modernas y tradicionales con una mirada respetuosa y empática.</p>
                @endif
                <div class="t-mod__about-stats">
                    <span class="t-mod__about-pie-foto">{{ phrase('about_pie_foto', '+10 años acompañando') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
