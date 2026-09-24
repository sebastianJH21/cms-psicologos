@php
    $faqs = $faqs ?? \App\Models\Faq::where('activa', true)->orderBy('orden')->get();
@endphp
@if ($faqs->isNotEmpty())
<section class="t-aurora__faq" id="faq">
    <div class="t-aurora__container t-aurora__container--narrow">
        <header class="t-aurora__sec-head">
            <span class="t-aurora__overline">{{ phrase('faq_overline', '— Preguntas frecuentes') }}</span>
            <h2 class="t-aurora__h2">{{ phrase('faq_title', 'Resolvemos tus dudas') }}</h2>
            @if (phrase('faq_description'))<p>{{ phrase('faq_description') }}</p>@endif
        </header>
        <div class="t-aurora__faqs">
            @foreach ($faqs as $faq)
                <details class="t-aurora__faq-item">
                    <summary>
                        <span>{{ $faq->pregunta }}</span>
                        <i class="fa-solid fa-plus"></i>
                    </summary>
                    <div class="t-aurora__faq-body wysiwyg">{!! $faq->respuesta !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
