@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-violeta__section t-violeta__section--alt" id="faq">
    <div class="t-violeta__container t-violeta__container--narrow">
        <div class="t-violeta__section-header">
            <span class="t-violeta__overtitle">{{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-violeta__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
        </div>
        <div class="t-violeta__faqs">
            @foreach ($faqs as $faq)
                <details class="t-violeta__faq">
                    <summary>{{ $faq->pregunta }} <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
