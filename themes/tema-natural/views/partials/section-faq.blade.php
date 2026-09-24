@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-natural__section t-natural__section--alt" id="faq">
    <div class="t-natural__container t-natural__container--narrow">
        <div class="t-natural__section-header">
            <span class="t-natural__overtitle">{{ phrase('faq_overline', 'Preguntas frecuentes') }}</span>
            <h2 class="t-natural__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
        </div>
        <div class="t-natural__faqs">
            @foreach ($faqs as $faq)
                <details class="t-natural__faq">
                    <summary>{{ $faq->pregunta }} <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
