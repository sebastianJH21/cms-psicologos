@php
    $aboutImg = theme_image('sobre-mi') ?: ($profile?->foto_path ? asset('storage/' . $profile->foto_path) : null);
@endphp
<section class="t-aurora__about" id="about">
    <div class="t-aurora__container">
        <div class="t-aurora__about-grid">
            <div class="t-aurora__about-text">
                <span class="t-aurora__overline">{{ phrase('about_overline', '— Sobre mí') }}</span>
                <h2 class="t-aurora__h2">{{ phrase('about_title', 'Una mirada cálida y profesional') }}</h2>
                @if ($profile?->sobre_mi)
                    <div class="t-aurora__about-body wysiwyg">{!! $profile->sobre_mi !!}</div>
                @else
                    <div class="t-aurora__about-body">
                        <p>Creo en una psicología cercana, sin etiquetas, donde tu historia es lo más importante. Trabajo desde la evidencia y la calma.</p>
                    </div>
                @endif

                @if (isset($terapias) && $terapias->isNotEmpty())
                    <ul class="t-aurora__check-list">
                        @foreach ($terapias->take(4) as $terapia)
                            <li><i class="fa-solid fa-check"></i> {{ $terapia->titulo }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="t-aurora__about-visual">
                @if ($aboutImg)
                    <div class="t-aurora__about-img">
                        <img src="{{ $aboutImg }}" alt="{{ $user?->nombre ?? 'Psicóloga' }}">
                    </div>
                @endif
                <blockquote class="t-aurora__quote">
                    <i class="fa-solid fa-quote-left"></i>
                    <p>{{ phrase('about_pie_foto', '+10 años acompañando') }}</p>
                    <cite>— {{ $user?->nombre }} {{ $user?->apellidos }}</cite>
                </blockquote>
            </div>
        </div>
    </div>
</section>
