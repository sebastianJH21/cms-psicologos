@if (($features['faq'] ?? true) && isset($faqs) && $faqs->isNotEmpty())
<div class="layout__faq-section" id="faq">
    <div class="faq-section__container">
        <header class="faq-section__header">
            <h4 class="faq-section__overline">{{ phrase('faq_overline', 'Dudas frecuentes') }}</h4>
            <h2 class="faq-section__title">{{ phrase('faq_title', 'Preguntas frecuentes') }}</h2>
            @if (phrase('faq_description'))
                <p class="faq-section__description">{{ phrase('faq_description') }}</p>
            @endif
        </header>

        <div class="faq-section__list">
            @foreach ($faqs as $faq)
                <details class="faq-item">
                    <summary class="faq-item__summary">
                        <span>{{ $faq->pregunta }}</span>
                        <i class="fa-solid fa-plus faq-item__icon"></i>
                    </summary>
                    <div class="faq-item__body wysiwyg">
                        {!! $faq->respuesta !!}
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</div>
@endif
