@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-sage__section t-sage__section--alt" id="faq">
    <div class="t-sage__container t-sage__container--narrow">
        <div class="t-sage__section-header">
            <span class="t-sage__overtitle">{{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-sage__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
        </div>
        <div class="t-sage__faqs">
            @foreach ($faqs as $faq)
                <details class="t-sage__faq">
                    <summary>{{ $faq->pregunta }} <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
