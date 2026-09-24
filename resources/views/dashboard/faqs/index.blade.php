@extends('dashboard.layout')

@section('titulo', 'Preguntas frecuentes')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Preguntas frecuentes'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/faqs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/frases.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title"><i class="fa-solid fa-circle-question" aria-hidden="true"></i> Preguntas frecuentes</h1>
            <p class="page-header__subtitle">Gestiona las FAQ que se mostrarán en la web pública. Arrastra para reordenar.</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.faqs.create') }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nueva pregunta
            </a>
        </div>
    </header>

    @if ($faqs->isEmpty())
        <div class="empty-state panel">
            <i class="fa-solid fa-circle-question empty-state__icon" aria-hidden="true"></i>
            <p class="empty-state__title">Sin preguntas frecuentes</p>
            <p class="empty-state__hint">Las FAQ ayudan a tus pacientes a resolver dudas habituales.</p>
            <a href="{{ route('dashboard.faqs.create') }}" class="btn btn--primary" style="margin-top:1.2rem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Crear primera pregunta
            </a>
        </div>
    @else
        <ul class="faq-list" id="faq-list" data-reorder-url="{{ route('dashboard.faqs.reordenar') }}"
            data-csrf="{{ csrf_token() }}">
            @foreach ($faqs as $faq)
                <li class="faq-item panel" draggable="true" data-id="{{ $faq->id }}">
                    <span class="faq-item__handle" aria-label="Mover" title="Arrastrar para reordenar">
                        <i class="fa-solid fa-grip-vertical" aria-hidden="true"></i>
                    </span>
                    <div class="faq-item__body">
                        <h3 class="faq-item__pregunta">
                            {{ $faq->pregunta }}
                            @if (!$faq->activa)
                                <span class="badge badge--warning">Inactiva</span>
                            @endif
                        </h3>
                        <p class="faq-item__respuesta">{{ Str::limit($faq->respuesta, 180) }}</p>
                    </div>
                    <div class="faq-item__actions">
                        <a href="{{ route('dashboard.faqs.edit', $faq) }}" class="btn btn--icon" aria-label="Editar">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                        </a>
                        <form method="POST" action="{{ route('dashboard.faqs.destroy', $faq) }}"
                            data-confirm="¿Eliminar esta pregunta? Esta acción no se puede deshacer."
                            data-ajax-delete="true"
                            data-delete-target="li"
                            style="display:contents">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--icon btn--icon-danger" aria-label="Eliminar">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/faqs.js') }}" defer></script>
@endpush
