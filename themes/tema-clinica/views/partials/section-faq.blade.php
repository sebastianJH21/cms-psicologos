@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-clinica__section t-clinica__section--alt" id="faq">
    <div class="t-clinica__container t-clinica__container--narrow">
        <div class="t-clinica__section-header">
            <span class="t-clinica__overtitle">{{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-clinica__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
        </div>
        <div class="t-clinica__faqs">
            @foreach ($faqs as $faq)
                <details class="t-clinica__faq">
                    <summary>{{ $faq->pregunta }} <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
