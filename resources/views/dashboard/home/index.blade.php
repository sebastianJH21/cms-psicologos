@extends('dashboard.layout')

@section('titulo', 'Inicio')

@php
    $breadcrumbs = [
        ['label' => 'Inicio'],
    ];
    $usuario = auth()->user();
@endphp

@push('scripts')
    <script src="{{ asset('js/dashboard/home.js') }}"></script>
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">¡Hola, Psc. {{ $usuario->nombre }}!</h1>
            <p class="page-header__subtitle">Aquí tienes un resumen de tu actividad de hoy.</p>
        </div>
        <a href="{{ route('dashboard.citas.create') }}" class="btn btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            <span>Nueva cita</span>
        </a>
    </header>

    <section class="stats-grid" aria-label="Resumen general">
        <article class="stat-card stat-card--keep-mobile">
            <div class="stat-card__icon stat-card__icon--primary">
                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
            </div>
            <div class="stat-card__body">
                <span class="stat-card__label">Próximas citas (hoy)</span>
                <strong class="stat-card__value">{{ $stats['proximas_citas_hoy'] }}</strong>
                <span class="stat-card__hint">Datos en tiempo real</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-card__icon stat-card__icon--success">
                <i class="fa-solid fa-user-group" aria-hidden="true"></i>
            </div>
            <div class="stat-card__body">
                <span class="stat-card__label">Pacientes activos</span>
                <strong class="stat-card__value">{{ $stats['pacientes_activos'] }}</strong>
                <span class="stat-card__hint">Datos en tiempo real</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-card__icon stat-card__icon--warning">
                <i class="fa-regular fa-newspaper" aria-hidden="true"></i>
            </div>
            <div class="stat-card__body">
                <span class="stat-card__label">Artículos publicados</span>
                <strong class="stat-card__value">{{ $stats['articulos_publicados'] }}</strong>
                <span class="stat-card__hint">Datos en tiempo real</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-card__icon stat-card__icon--info">
                <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
            </div>
            <div class="stat-card__body">
                <span class="stat-card__label">Sesiones del mes</span>
                <strong class="stat-card__value">{{ $stats['sesiones_mes'] }}</strong>
                <span class="stat-card__hint">Datos en tiempo real</span>
            </div>
        </article>
    </section>

    <div class="dashboard-grid">
        <section class="panel" aria-labelledby="panel-citas-title">
            <header class="panel__header">
                <h2 class="panel__title" id="panel-citas-title">
                    <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                    Próximas citas de hoy
                </h2>
                <a href="{{ route('dashboard.citas.index') }}" class="panel__link">Ver todas</a>
            </header>

            @if (count($proximasCitas) === 0)
                @php
                    $hayCitasFuturas = \App\Models\Cita::where('fecha_inicio', '>', now()->endOfDay())
                        ->whereNotIn('estado', ['cancelada'])
                        ->exists();
                @endphp
                <div class="empty-state">
                    <i class="fa-regular fa-calendar-check empty-state__icon" aria-hidden="true"></i>
                    <p class="empty-state__title">Hoy no tienes citas agendadas</p>
                    <p class="empty-state__hint">Cuando tengas citas para hoy aparecerán aquí.</p>
                    @if ($hayCitasFuturas)
                        <a href="{{ route('dashboard.citas.index') }}" class="btn btn--primary empty-state__action">
                            <i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
                            <span>Citas para los próximos días</span>
                        </a>
                    @endif
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Hora</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Modalidad</th>
                            <th scope="col" class="data-table__actions">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proximasCitas as $cita)
                            <tr>
                                <td>
                                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                    <strong>{{ $cita['hora'] }}</strong>
                                </td>
                                <td>{{ $cita['paciente'] }}</td>
                                <td>
                                    <span class="badge badge--{{ $cita['modalidad'] === 'online' ? 'info' : 'warning' }}">
                                        {{ $cita['modalidad_label'] }}
                                    </span>
                                </td>
                                <td class="data-table__actions">
                                    <a href="{{ $cita['url'] }}" class="btn btn--icon" aria-label="Ver detalle">
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section class="panel" aria-labelledby="panel-disponibilidad-title">
            <header class="panel__header">
                <h2 class="panel__title" id="panel-disponibilidad-title">
                    <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                    Disponibilidad semanal
                </h2>
                <a href="{{ route('dashboard.disponibilidad.edit') }}" class="panel__link">
                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                </a>
            </header>

            <div class="availability-tabs" role="tablist">
                <button type="button" class="availability-tabs__btn availability-tabs__btn--active" role="tab" aria-selected="true" data-availability-tab="presencial">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    Presencial
                </button>
                <button type="button" class="availability-tabs__btn" role="tab" aria-selected="false" data-availability-tab="online">
                    <i class="fa-solid fa-video" aria-hidden="true"></i>
                    Online
                </button>
            </div>

            @foreach (['presencial', 'online'] as $modalidad)
                <ul class="availability-list {{ $modalidad === 'presencial' ? 'availability-list--active' : '' }}" data-availability-modalidad="{{ $modalidad }}" role="tabpanel">
                    @foreach ($disponibilidadSemanal[$modalidad] as $dia)
                        <li class="availability-list__item">
                            <span class="availability-list__day">{{ $dia['dia'] }}</span>
                            @if ($dia['rango'])
                                <span class="availability-list__range">{{ $dia['rango'] }}</span>
                            @else
                                <span class="availability-list__range availability-list__range--off">No configurado</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </section>
    </div>

    <section class="panel panel--soft" aria-labelledby="panel-onboarding-title">
        <header class="panel__header">
            <h2 class="panel__title" id="panel-onboarding-title">
                <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                Próximos pasos sugeridos
            </h2>
            <span class="panel__link">{{ $pasos['completados'] }}/{{ $pasos['total'] }} completados</span>
        </header>

        <div class="onboarding-progress" role="progressbar" aria-valuenow="{{ $pasos['porcentaje'] }}" aria-valuemin="0" aria-valuemax="100">
            <div class="onboarding-progress__bar" style="width: {{ $pasos['porcentaje'] }}%"></div>
        </div>

        @if ($pasos['todo_completado'])
            <div class="onboarding-done">
                <i class="fa-solid fa-circle-check onboarding-done__icon" aria-hidden="true"></i>
                <div>
                    <strong class="onboarding-done__title">¡Tu PsicoCMS está totalmente configurado!</strong>
                    <p class="onboarding-done__text">Has completado todos los pasos sugeridos. Ya puedes empezar a recibir reservas y gestionar tu consulta.</p>
                </div>
            </div>
        @else
            <ul class="checklist">
                @foreach ($pasos['items'] as $paso)
                    <li class="checklist__item {{ $paso['completed'] ? 'checklist__item--done' : '' }}">
                        @if ($paso['completed'])
                            <i class="fa-solid fa-circle-check checklist__icon checklist__icon--done" aria-hidden="true"></i>
                            <span>{{ $paso['label'] }}</span>
                        @else
                            <i class="fa-regular fa-circle checklist__icon" aria-hidden="true"></i>
                            @if (!empty($paso['url']))
                                <a href="{{ $paso['url'] }}" class="checklist__link">
                                    {{ $paso['label'] }}
                                    <i class="fa-solid fa-arrow-right checklist__arrow" aria-hidden="true"></i>
                                </a>
                            @else
                                <span>{{ $paso['label'] }}</span>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
