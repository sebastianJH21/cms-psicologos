@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-mod__section" id="faq">
    <div class="t-mod__container t-mod__container--narrow">
        <div class="t-mod__sec-head">
            <span class="t-mod__overline">{{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-mod__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
            @if (phrase('faq_description'))<p>{{ phrase('faq_description') }}</p>@endif
        </div>
        <div class="t-mod__faqs">
            @foreach ($faqs as $faq)
                <details class="t-mod__faq">
                    <summary>{{ $faq->pregunta }}<i class="fa-solid fa-plus"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
