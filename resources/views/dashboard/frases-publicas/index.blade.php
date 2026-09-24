@extends('dashboard.layout')

@section('titulo', 'Frases públicas')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Gestión Web'],
        ['label' => 'Frases públicas'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-quote-right" aria-hidden="true"></i>
                Frases públicas
            </h1>
            <p class="page-header__subtitle">
                Personaliza todos los textos que aparecen en la web pública, por secciones.
            </p>
        </div>
    </header>

    @include('dashboard.partials.frases-form', [
        'seccion' => 'hero',
        'titulo' => 'Frases del Hero (inicio)',
        'descripcion' => 'Texto pequeño y CTA del bloque principal.',
        'campos' => [
            ['key' => 'hero_badge', 'label' => 'Etiqueta breve (badge)', 'icon' => 'tag', 'hint' => 'Texto corto que aparece arriba en el hero (ej: "Psicología clínica")'],
            ['key' => 'hero_overline', 'label' => 'Etiqueta encima del título', 'icon' => 'tag'],
            ['key' => 'hero_cta', 'label' => 'Texto del botón principal', 'icon' => 'arrow-pointer'],
            ['key' => 'hero_frase', 'label' => 'Frase del hero (título o subtítulo, según el tema)', 'icon' => 'quote-right', 'type' => 'textarea', 'wide' => true, 'hint' => 'Aparece como título o subtítulo destacado del hero, según el tema activo. Si lo dejas vacío se usa una frase por defecto.'],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'about',
        'titulo' => 'Frases de Sobre mí',
        'descripcion' => 'Textos del bloque "Sobre mí".',
        'campos' => [
            ['key' => 'about_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'about_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'about_pie_foto', 'label' => 'Pie de foto', 'icon' => 'image', 'hint' => 'Texto corto que aparece sobre la imagen en la sección Sobre mí (ej: "+10 años acompañando").'],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'servicios',
        'titulo' => 'Frases de Servicios',
        'descripcion' => 'Textos sobre el listado de servicios.',
        'campos' => [
            ['key' => 'servicios_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'servicios_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'servicios_description', 'label' => 'Descripción', 'icon' => 'align-left', 'type' => 'textarea', 'wide' => true],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'especialidades',
        'titulo' => 'Frases de Especialidades',
        'descripcion' => 'Textos sobre el listado de especialidades.',
        'campos' => [
            ['key' => 'especialidades_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'especialidades_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'especialidades_description', 'label' => 'Descripción', 'icon' => 'align-left', 'type' => 'textarea', 'wide' => true],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'planes',
        'titulo' => 'Frases de Planes y precios',
        'descripcion' => 'Textos sobre el listado de planes.',
        'campos' => [
            ['key' => 'planes_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'planes_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'planes_description', 'label' => 'Descripción', 'icon' => 'align-left', 'type' => 'textarea', 'wide' => true],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'blog',
        'titulo' => 'Frases del Blog',
        'descripcion' => 'Textos sobre el listado de artículos.',
        'campos' => [
            ['key' => 'blog_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'blog_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'blog_description', 'label' => 'Descripción', 'icon' => 'align-left', 'type' => 'textarea', 'wide' => true],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'faq',
        'titulo' => 'Frases de Preguntas frecuentes',
        'descripcion' => 'Textos sobre el listado de FAQ.',
        'campos' => [
            ['key' => 'faq_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'faq_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'faq_description', 'label' => 'Descripción', 'icon' => 'align-left', 'type' => 'textarea', 'wide' => true],
        ],
    ])

    @include('dashboard.partials.frases-form', [
        'seccion' => 'cita',
        'titulo' => 'Frases de Pide cita',
        'descripcion' => 'Textos sobre el formulario de reservas.',
        'campos' => [
            ['key' => 'cita_overline', 'label' => 'Sobretítulo', 'icon' => 'tag'],
            ['key' => 'cita_title', 'label' => 'Título principal', 'icon' => 'heading'],
            ['key' => 'cita_description', 'label' => 'Descripción', 'icon' => 'align-left', 'type' => 'textarea', 'wide' => true],
        ],
    ])
@endsection
