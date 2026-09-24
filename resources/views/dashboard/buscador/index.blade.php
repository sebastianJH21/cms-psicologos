@extends('dashboard.layout')

@section('titulo', 'Resultados de búsqueda')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Búsqueda'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/buscador.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                Resultados de búsqueda
            </h1>
            @if (mb_strlen($q) >= 2)
                <p class="page-header__subtitle">
                    {{ $total }} {{ $total === 1 ? 'coincidencia' : 'coincidencias' }} para
                    <strong>"{{ $q }}"</strong>
                </p>
            @else
                <p class="page-header__subtitle">Escribe al menos 2 caracteres en el buscador del header.</p>
            @endif
        </div>
    </header>

    @if (mb_strlen($q) < 2)
        <section class="panel">
            <div class="empty-state">
                <i class="fa-solid fa-keyboard empty-state__icon" aria-hidden="true"></i>
                <p class="empty-state__title">Realiza una búsqueda</p>
                <p class="empty-state__hint">Puedes buscar pacientes, citas, historias clínicas, artículos del blog y preguntas frecuentes a la vez.</p>
            </div>
        </section>
    @elseif ($total === 0)
        <section class="panel">
            <div class="empty-state">
                <i class="fa-solid fa-face-frown empty-state__icon" aria-hidden="true"></i>
                <p class="empty-state__title">No hemos encontrado nada para "{{ $q }}"</p>
                <p class="empty-state__hint">Intenta usar otras palabras clave o partes del nombre/teléfono.</p>
            </div>
        </section>
    @else
        @if ($resultados['pacientes']->isNotEmpty())
            <section class="panel">
                <h2 class="panel__title">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    Pacientes ({{ $resultados['pacientes']->count() }})
                </h2>
                <ul class="search-list">
                    @foreach ($resultados['pacientes'] as $paciente)
                        <li class="search-list__item">
                            <a href="{{ route('dashboard.pacientes.show', $paciente) }}" class="search-list__link">
                                <i class="fa-solid fa-user search-list__icon" aria-hidden="true"></i>
                                <span class="search-list__main">
                                    <strong>{{ $paciente->nombre }} {{ $paciente->apellidos }}</strong>
                                    <span class="search-list__meta">
                                        @if ($paciente->telefono)
                                            <i class="fa-solid fa-phone" aria-hidden="true"></i> {{ $paciente->telefono }}
                                        @endif
                                        @if ($paciente->email)
                                            · <i class="fa-solid fa-envelope" aria-hidden="true"></i> {{ $paciente->email }}
                                        @endif
                                    </span>
                                </span>
                                <i class="fa-solid fa-chevron-right search-list__chevron" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($resultados['citas']->isNotEmpty())
            <section class="panel">
                <h2 class="panel__title">
                    <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                    Citas ({{ $resultados['citas']->count() }})
                </h2>
                <ul class="search-list">
                    @foreach ($resultados['citas'] as $cita)
                        <li class="search-list__item">
                            <a href="{{ route('dashboard.citas.show', $cita) }}" class="search-list__link">
                                <i class="fa-solid fa-calendar-day search-list__icon" aria-hidden="true"></i>
                                <span class="search-list__main">
                                    <strong>{{ $cita->nombre_provisional ?? ($cita->paciente?->nombre_completo ?? 'Sin nombre') }}</strong>
                                    <span class="search-list__meta">
                                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                        {{ $cita->fecha_inicio->format('d/m/Y H:i') }}
                                        ·
                                        <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">{{ ucfirst($cita->modalidad) }}</span>
                                        ·
                                        <span>{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                                    </span>
                                    @if ($cita->motivo)
                                        <span class="search-list__excerpt">{{ \Illuminate\Support\Str::limit($cita->motivo, 140) }}</span>
                                    @endif
                                </span>
                                <i class="fa-solid fa-chevron-right search-list__chevron" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($resultados['historias']->isNotEmpty())
            <section class="panel">
                <h2 class="panel__title">
                    <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                    Historias clínicas ({{ $resultados['historias']->count() }})
                </h2>
                <ul class="search-list">
                    @foreach ($resultados['historias'] as $historia)
                        <li class="search-list__item">
                            <a href="{{ route('dashboard.pacientes.historias.show', [$historia->paciente_id, $historia]) }}" class="search-list__link">
                                <i class="fa-solid fa-file-medical search-list__icon" aria-hidden="true"></i>
                                <span class="search-list__main">
                                    <strong>{{ $historia->titulo_mostrado }}</strong>
                                    <span class="search-list__meta">
                                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                                        {{ $historia->paciente?->nombre_completo ?? 'Paciente desconocido' }}
                                        ·
                                        {{ $historia->fecha_sesion?->format('d/m/Y') }}
                                    </span>
                                    <span class="search-list__excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($historia->contenido), 160) }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-right search-list__chevron" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($resultados['articulos']->isNotEmpty())
            <section class="panel">
                <h2 class="panel__title">
                    <i class="fa-solid fa-newspaper" aria-hidden="true"></i>
                    Artículos del blog ({{ $resultados['articulos']->count() }})
                </h2>
                <ul class="search-list">
                    @foreach ($resultados['articulos'] as $articulo)
                        <li class="search-list__item">
                            <a href="{{ route('dashboard.blog.articulos.edit', $articulo) }}" class="search-list__link">
                                <i class="fa-solid fa-file-lines search-list__icon" aria-hidden="true"></i>
                                <span class="search-list__main">
                                    <strong>{{ $articulo->titulo }}</strong>
                                    <span class="search-list__meta">
                                        <span class="badge badge--{{ $articulo->estado === 'publicado' ? 'success' : 'muted' }}">{{ ucfirst($articulo->estado) }}</span>
                                        @if ($articulo->published_at)
                                            · {{ $articulo->published_at->format('d/m/Y') }}
                                        @endif
                                    </span>
                                    @if ($articulo->extracto)
                                        <span class="search-list__excerpt">{{ \Illuminate\Support\Str::limit($articulo->extracto, 160) }}</span>
                                    @endif
                                </span>
                                <i class="fa-solid fa-chevron-right search-list__chevron" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($resultados['faqs']->isNotEmpty())
            <section class="panel">
                <h2 class="panel__title">
                    <i class="fa-solid fa-circle-question" aria-hidden="true"></i>
                    Preguntas frecuentes ({{ $resultados['faqs']->count() }})
                </h2>
                <ul class="search-list">
                    @foreach ($resultados['faqs'] as $faq)
                        <li class="search-list__item">
                            <a href="{{ route('dashboard.faqs.edit', $faq) }}" class="search-list__link">
                                <i class="fa-solid fa-question search-list__icon" aria-hidden="true"></i>
                                <span class="search-list__main">
                                    <strong>{{ $faq->pregunta }}</strong>
                                    <span class="search-list__excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($faq->respuesta), 160) }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-right search-list__chevron" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif
@endsection
