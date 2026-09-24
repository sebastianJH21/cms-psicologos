@php
    $articulos = $articulos ?? \App\Models\Articulo::publicados()->orderByDesc('published_at')->limit(3)->get();
    $isLanding = ($themeMode ?? 'landing') === 'landing';
@endphp
@if ($articulos->isNotEmpty())
<section class="t-aurora__blog" id="blog">
    <div class="t-aurora__container">
        <header class="t-aurora__sec-head t-aurora__sec-head--split">
            <div>
                <span class="t-aurora__overline">{{ phrase('blog_overline', '— Diario') }}</span>
                <h2 class="t-aurora__h2">{{ phrase('blog_title', 'Reflexiones desde la consulta') }}</h2>
                @if (phrase('blog_description'))<p>{{ phrase('blog_description') }}</p>@endif
            </div>
            <a href="{{ url('/blog') }}" class="t-aurora__sec-link">
                Ver todo el diario <i class="fa-solid fa-arrow-right"></i>
            </a>
        </header>
        <div class="t-aurora__posts">
            @foreach ($articulos as $articulo)
                <article class="t-aurora__post">
                    <a href="{{ url('/blog/' . $articulo->slug) }}">
                        <div class="t-aurora__post-img">
                            @if ($articulo->imagen_path)
                                <img src="{{ asset('storage/' . $articulo->imagen_path) }}" alt="{{ $articulo->titulo }}">
                            @else
                                <div class="t-aurora__post-placeholder"><i class="fa-solid fa-feather"></i></div>
                            @endif
                        </div>
                        <div class="t-aurora__post-meta">
                            @if ($articulo->categoria)<span class="t-aurora__post-cat">{{ $articulo->categoria->nombre }}</span>@endif
                            <time>{{ $articulo->published_at?->translatedFormat('d M Y') }}</time>
                        </div>
                        <h3>{{ $articulo->titulo }}</h3>
                        <span class="t-aurora__post-cta">Leer artículo <i class="fa-solid fa-arrow-right"></i></span>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
