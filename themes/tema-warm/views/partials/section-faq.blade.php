@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-warm__section t-warm__section--alt" id="faq">
    <div class="t-warm__container t-warm__container--narrow">
        <div class="t-warm__sec-head">
            <span class="t-warm__overline">— {{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-warm__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
            @if (phrase('faq_description'))<p>{{ phrase('faq_description') }}</p>@endif
        </div>
        <div class="t-warm__faqs">
            @foreach ($faqs as $faq)
                <details class="t-warm__faq">
                    <summary>{{ $faq->pregunta }}<i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
