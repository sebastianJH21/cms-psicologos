@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/jodit/jodit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/wysiwyg.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('vendor/jodit/jodit.min.js') }}" defer></script>
        <script src="{{ asset('js/dashboard/jodit-init.js') }}" defer></script>
    @endpush
@endonce
