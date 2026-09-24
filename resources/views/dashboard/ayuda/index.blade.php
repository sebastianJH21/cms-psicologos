@extends('dashboard.layout')

@section('titulo', 'Ayuda y tutorial')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Ayuda'],
    ];

    $secciones = [
        [
            'icon' => 'fa-house',
            'title' => 'Inicio del panel',
            'desc' => 'En la pantalla principal del dashboard ves un resumen de tu actividad: próximas citas de hoy, número de pacientes activos, artículos publicados y sesiones realizadas este mes. También encontrarás los <strong>Próximos pasos sugeridos</strong> que te guían para terminar de configurar tu PsicoCMS.',
        ],
        [
            'icon' => 'fa-calendar-check',
            'title' => 'Citas',
            'desc' => 'Aquí gestionas todas las citas, vengan de la web o las hayas creado a mano. Puedes filtrar por modalidad, estado, paciente y fechas. Al crear una cita manualmente, el campo de nombre tiene un buscador inteligente: si el paciente ya existe lo encuentras y se rellena solo, si no, se crea automáticamente al guardar.',
        ],
        [
            'icon' => 'fa-calendar-days',
            'title' => 'Calendario',
            'desc' => 'Vista mensual estilo Google Calendar. Las citas aparecen con un color por modalidad. Puedes hacer clic en un hueco para crear una cita rápida o sobre una cita existente para verla o editarla.',
        ],
        [
            'icon' => 'fa-clock',
            'title' => 'Disponibilidad',
            'desc' => 'En "Configuración → Disponibilidad" defines tus horarios <strong>online y presenciales</strong> por separado. Marca con un clic los huecos disponibles de cada día. También tienes el interruptor de <strong>modo vacaciones</strong> que pausa las reservas públicas sin que pierdas la configuración.',
        ],
        [
            'icon' => 'fa-users',
            'title' => 'Pacientes',
            'desc' => 'Cada paciente se identifica de forma única por su teléfono. Puedes crear pacientes manualmente o se crearán automáticamente cuando reserven cita desde tu web. Desde la ficha del paciente se accede a su historial de citas, sus historias clínicas y al PDF de protección de datos pre-rellenado.',
        ],
        [
            'icon' => 'fa-folder-open',
            'title' => 'Historias clínicas',
            'desc' => 'Para cada paciente puedes crear historias de seguimiento con un editor enriquecido y subir adjuntos (imágenes o PDFs escaneados). Los archivos se guardan de forma privada y solo se sirven a través del panel autenticado.',
        ],
        [
            'icon' => 'fa-newspaper',
            'title' => 'Blog',
            'desc' => 'Crea, edita y publica artículos para tu web. Cada artículo tiene una imagen destacada y se vincula a una categoría. Usa el editor Jodit para dar formato al contenido.',
        ],
        [
            'icon' => 'fa-globe',
            'title' => 'Gestión Web',
            'desc' => 'En este grupo configuras todo lo que se ve en tu web pública: información personal, servicios, terapias, planes y precios, FAQs e imágenes destacadas del tema. También seleccionas la plantilla visual desde "Temas".',
        ],
        [
            'icon' => 'fa-shield-halved',
            'title' => 'Protección de datos',
            'desc' => 'En "Configuración → Protección de datos" diseñas la plantilla de tu documento legal con marcadores como <code>{{nombre}}</code>, <code>{{telefono}}</code>, etc. Después, desde la ficha de cada paciente puedes descargar el PDF ya rellenado.',
        ],
        [
            'icon' => 'fa-envelope',
            'title' => 'Email y notificaciones',
            'desc' => 'Configura un correo de Gmail (con contraseña de aplicación) en "Configuración → Email y notificaciones" y empezarás a recibir un email cada vez que un paciente reserve cita en tu web.',
        ],
        [
            'icon' => 'fa-share-nodes',
            'title' => 'Redes sociales',
            'desc' => 'Pega los enlaces a tus perfiles sociales (Instagram, Facebook, LinkedIn, TikTok, YouTube, Twitter/X y WhatsApp). Los iconos aparecerán automáticamente en el footer de tu web pública.',
        ],
        [
            'icon' => 'fa-sliders',
            'title' => 'General',
            'desc' => 'Activa o desactiva las secciones de tu web (Blog, Reservas, FAQ, Servicios, Sobre mí). Los cambios se guardan al instante con cada toggle.',
        ],
        [
            'icon' => 'fa-palette',
            'title' => 'Apariencia del dashboard',
            'desc' => 'Pulsa el icono de paleta de colores en la barra lateral para abrir la ventana de apariencia: elige tema claro u oscuro y un color primario que se aplicará a todo tu panel. La preferencia se queda guardada aunque cierres sesión.',
        ],
        [
            'icon' => 'fa-bell',
            'title' => 'Notificaciones',
            'desc' => 'El icono de la campana en el header del dashboard te avisa con un punto rojo cuando hay nuevas reservas hechas por pacientes. Al entrar verás la lista de las recientes y al volver desaparecerán del contador.',
        ],
        [
            'icon' => 'fa-magnifying-glass',
            'title' => 'Buscador global',
            'desc' => 'En la parte superior tienes un buscador único que cruza pacientes, citas, historias clínicas, artículos y FAQs. Escribe al menos 2 caracteres y pulsa Enter.',
        ],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/ayuda.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                Ayuda y tutorial
            </h1>
            <p class="page-header__subtitle">Una guía rápida para sacarle el máximo partido a tu PsicoCMS.</p>
        </div>
    </header>

    <section class="panel ayuda-intro">
        <div class="ayuda-intro__icon">
            <i class="fa-solid fa-lightbulb" aria-hidden="true"></i>
        </div>
        <div>
            <h2 class="ayuda-intro__title">Bienvenida a PsicoCMS</h2>
            <p class="ayuda-intro__text">
                PsicoCMS es tu panel privado para gestionar tu web pública, tus citas y tus pacientes desde un solo sitio. Está pensado para que sea sencillo de usar aunque no tengas experiencia técnica. A continuación tienes una explicación de cada parte del panel. Si ves cualquier botón o etiqueta y no sabes qué hace, vuelve a esta página.
            </p>
        </div>
    </section>

    <section class="panel">
        <h2 class="panel__title">
            <i class="fa-solid fa-list" aria-hidden="true"></i>
            Secciones del panel
        </h2>

        <div class="ayuda-grid">
            @foreach ($secciones as $seccion)
                <article class="ayuda-card">
                    <div class="ayuda-card__icon">
                        <i class="fa-solid {{ $seccion['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <h3 class="ayuda-card__title">{{ $seccion['title'] }}</h3>
                    <p class="ayuda-card__text">{!! $seccion['desc'] !!}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="panel ayuda-tips">
        <h2 class="panel__title">
            <i class="fa-solid fa-star" aria-hidden="true"></i>
            Consejos prácticos
        </h2>
        <ul class="ayuda-tips__list">
            <li><i class="fa-solid fa-check" aria-hidden="true"></i> Revisa los <strong>Próximos pasos sugeridos</strong> de la home: te indicarán qué te falta por configurar.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i> Antes de abrir tu web al público, configura siempre tu <strong>disponibilidad</strong> y comprueba el formulario de reservas.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i> Configura el <strong>SMTP</strong> (Email y notificaciones) para no perder ninguna reserva.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i> Usa la <strong>papelera de pacientes</strong> para recuperar fichas eliminadas por error.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i> Activa el <strong>modo vacaciones</strong> en lugar de borrar tu disponibilidad cuando vayas a parar las reservas temporalmente.</li>
        </ul>
    </section>

    <section class="panel ayuda-soporte">
        <div>
            <h2 class="ayuda-soporte__title">
                <i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i>
                ¿Necesitas más ayuda?
            </h2>
            <p class="ayuda-soporte__text">Si tienes dudas concretas o quieres pedir un diseño personalizado para tu web, contacta con el desarrollador.</p>
        </div>
        <a href="https://victorroblesweb.es/contacto" target="_blank" rel="noopener" class="btn btn--primary">
            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
            Contactar
        </a>
    </section>
@endsection
