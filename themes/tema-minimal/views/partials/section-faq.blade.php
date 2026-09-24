@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-min__section t-min__section--alt" id="faq">
    <div class="t-min__container t-min__container--narrow">
        <p class="t-min__overline">— {{ phrase('faq_overline', 'Preguntas frecuentes') }}</p>
        <h2 class="t-min__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
        <div class="t-min__faqs">
            @foreach ($faqs as $faq)
                <details class="t-min__faq">
                    <summary>{{ $faq->pregunta }}</summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
