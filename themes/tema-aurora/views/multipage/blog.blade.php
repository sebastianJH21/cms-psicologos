@extends('theme::layout')
@section('contenido')
<section class="t-aurora__blog-list">
    <div class="t-aurora__container">
        <header class="t-aurora__sec-head">
            <span class="t-aurora__overline">— Diario</span>
            <h2 class="t-aurora__h2">{{ phrase('blog_title', 'Diario de la consulta') }}</h2>
        </header>
        <div id="t-aurora__blog-ajax" data-base-url="{{ url('/blog') }}">
            @include('theme::multipage.blog-fragment')
        </div>
    </div>
</section>
@endsection
