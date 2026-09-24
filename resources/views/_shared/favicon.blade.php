@php $faviconData = function_exists('logo_data') ? logo_data() : ['url' => null, 'icon' => null]; @endphp
@if (!empty($faviconData['url']))
    <link rel="icon" href="{{ $faviconData['url'] }}">
    <link rel="apple-touch-icon" href="{{ $faviconData['url'] }}">
@elseif (!empty($faviconData['icon']))
    @php
        $iconColor = '#976147';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="32" r="30" fill="' . $iconColor . '"/><text x="50%" y="50%" font-family="FontAwesome,sans-serif" font-size="30" fill="white" text-anchor="middle" dominant-baseline="central">●</text></svg>';
    @endphp
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,{{ rawurlencode($svg) }}">
@endif
