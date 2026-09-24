<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ $tituloSitio }}</title>
    <link>{{ url('/blog') }}</link>
    <atom:link href="{{ url('/blog/rss.xml') }}" rel="self" type="application/rss+xml" />
    <description>{{ $descripcion }}</description>
    <language>es-ES</language>
    <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
    @foreach ($articulos as $articulo)
    <item>
        <title>{!! htmlspecialchars($articulo->titulo, ENT_XML1) !!}</title>
        <link>{{ route('public.blog.show', $articulo->slug) }}</link>
        <guid>{{ route('public.blog.show', $articulo->slug) }}</guid>
        <pubDate>{{ $articulo->published_at?->toRssString() }}</pubDate>
        <description>{!! htmlspecialchars(\Illuminate\Support\Str::limit(strip_tags($articulo->extracto ?? $articulo->contenido), 300), ENT_XML1) !!}</description>
        @if ($articulo->categoria)
            <category>{!! htmlspecialchars($articulo->categoria->nombre, ENT_XML1) !!}</category>
        @endif
    </item>
    @endforeach
</channel>
</rss>
