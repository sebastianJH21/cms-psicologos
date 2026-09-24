@if (!empty($breadcrumbs ?? []))
    <nav class="breadcrumbs" aria-label="Migas de pan">
        <ol class="breadcrumbs__list">
            @foreach ($breadcrumbs as $crumb)
                <li class="breadcrumbs__item">
                    @if (!empty($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="breadcrumbs__link">{{ $crumb['label'] }}</a>
                    @else
                        <span class="breadcrumbs__current" aria-current="page">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
