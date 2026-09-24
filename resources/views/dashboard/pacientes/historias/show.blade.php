@extends('dashboard.layout')

@section('titulo', $historia->titulo_mostrado)

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Pacientes', 'url' => route('dashboard.pacientes.index')],
        ['label' => $paciente->nombre_completo, 'url' => route('dashboard.pacientes.show', $paciente)],
        ['label' => 'Historia clínica', 'url' => route('dashboard.pacientes.historias.index', $paciente)],
        ['label' => $historia->titulo_mostrado],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/historias.css') }}">
@endpush

@section('contenido')
    <header class="page-header">
        <div>
            <h1 class="page-header__title">{{ $historia->titulo_mostrado }}</h1>
            <p class="page-header__subtitle">
                <i class="fa-solid fa-calendar" aria-hidden="true"></i>
                {{ $historia->fecha_sesion->translatedFormat('d \d\e F \d\e Y') }}
                &nbsp;&middot;&nbsp;
                <a href="{{ route('dashboard.pacientes.show', $paciente) }}">{{ $paciente->nombre_completo }}</a>
            </p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dashboard.pacientes.historias.index', $paciente) }}" class="btn btn--ghost">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Volver
            </a>
            <a href="{{ route('dashboard.pacientes.historias.edit', [$paciente, $historia]) }}" class="btn btn--ghost">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                Editar
            </a>
            <form method="POST"
                action="{{ route('dashboard.pacientes.historias.destroy', [$paciente, $historia]) }}"
                data-confirm="¿Eliminar esta entrada de historia? Esta acción no se puede deshacer."
                style="display:contents">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--danger">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    Eliminar
                </button>
            </form>
        </div>
    </header>

    {{-- Contenido de la sesión --}}
    <section class="panel">
        <header class="panel__header">
            <h2 class="panel__title">
                <i class="fa-solid fa-notes-medical" aria-hidden="true"></i>
                Notas de la sesión
            </h2>
            <span class="historia-sesion-fecha">
                {{ $historia->fecha_sesion->translatedFormat('l, d \d\e F \d\e Y') }}
            </span>
        </header>
        <div class="historia-contenido wysiwyg">
            {!! $historia->contenido !!}
        </div>
    </section>

    {{-- Archivos adjuntos --}}
    @if ($historia->archivos->isNotEmpty())
        <section class="panel">
            <header class="panel__header">
                <h2 class="panel__title">
                    <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                    Archivos adjuntos
                    <span class="badge badge--info" style="margin-left:0.6rem">{{ $historia->archivos->count() }}</span>
                </h2>
            </header>

            <div class="historia-archivos">
                @foreach ($historia->archivos as $archivo)
                    @if ($archivo->tipo === 'imagen')
                        <button type="button"
                            class="historia-archivo historia-archivo--imagen"
                            data-archivo-url="{{ $archivo->url }}"
                            data-archivo-tipo="imagen"
                            data-archivo-nombre="{{ $archivo->nombre_original }}"
                            title="{{ $archivo->nombre_original }}">
                            <img src="{{ $archivo->url }}" alt="{{ $archivo->nombre_original }}">
                            <span class="historia-archivo__nombre">{{ Str::limit($archivo->nombre_original, 20) }}</span>
                            <span class="historia-archivo__tamanio">{{ $archivo->tamanio_formateado }}</span>
                        </button>
                    @else
                        <button type="button"
                            class="historia-archivo historia-archivo--pdf"
                            data-archivo-url="{{ $archivo->url }}"
                            data-archivo-tipo="pdf"
                            data-archivo-nombre="{{ $archivo->nombre_original }}"
                            title="{{ $archivo->nombre_original }}">
                            <i class="fa-solid fa-file-pdf historia-archivo__pdf-icon" aria-hidden="true"></i>
                            <span class="historia-archivo__nombre">{{ Str::limit($archivo->nombre_original, 20) }}</span>
                            <span class="historia-archivo__tamanio">{{ $archivo->tamanio_formateado }}</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- Navegación inferior --}}
    <div class="historia-nav">
        <a href="{{ route('dashboard.pacientes.historias.create', $paciente) }}" class="btn btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Nueva entrada
        </a>
        <a href="{{ route('dashboard.pacientes.historias.index', $paciente) }}" class="btn btn--ghost">
            <i class="fa-solid fa-list" aria-hidden="true"></i>
            Ver toda la historia
        </a>
    </div>

    {{-- Modal visor de archivos --}}
    <div class="modal" id="modal-archivo" hidden aria-modal="true" role="dialog" aria-labelledby="modal-archivo-titulo">
        <div class="modal__backdrop" id="modal-archivo-backdrop"></div>
        <div class="modal__dialog modal__dialog--archivo">
            <div class="modal__header">
                <h2 class="modal__title" id="modal-archivo-titulo">Archivo</h2>
                <button type="button" class="modal__close" id="modal-archivo-close" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal__body modal__body--archivo" id="modal-archivo-body"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('modal-archivo');
    const body = document.getElementById('modal-archivo-body');
    const titulo = document.getElementById('modal-archivo-titulo');
    const closeBtn = document.getElementById('modal-archivo-close');
    const backdrop = document.getElementById('modal-archivo-backdrop');

    if (!modal) return;

    const open = (url, tipo, nombre) => {
        while (body.firstChild) body.removeChild(body.firstChild);

        titulo.textContent = nombre;

        if (tipo === 'imagen') {
            const img = document.createElement('img');
            img.src = url;
            img.alt = nombre;
            img.className = 'modal-archivo__imagen';
            body.appendChild(img);
        } else {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.title = nombre;
            iframe.className = 'modal-archivo__iframe';
            body.appendChild(iframe);
        }

        modal.hidden = false;
        closeBtn.focus();
    };

    const close = () => {
        modal.hidden = true;
        while (body.firstChild) body.removeChild(body.firstChild);
    };

    document.querySelectorAll('[data-archivo-url]').forEach((btn) => {
        btn.addEventListener('click', () => {
            open(
                btn.dataset.archivoUrl,
                btn.dataset.archivoTipo,
                btn.dataset.archivoNombre
            );
        });
    });

    closeBtn?.addEventListener('click', close);
    backdrop?.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) close();
    });
})();
</script>
@endpush
