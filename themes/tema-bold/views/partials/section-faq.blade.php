@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-bold__section t-bold__section--alt" id="faq">
    <div class="t-bold__container t-bold__container--narrow">
        <div class="t-bold__section-header">
            <span class="t-bold__overtitle">{{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-bold__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
        </div>
        <div class="t-bold__faqs">
            @foreach ($faqs as $faq)
                <details class="t-bold__faq">
                    <summary>{{ $faq->pregunta }} <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
