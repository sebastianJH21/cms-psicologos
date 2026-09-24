@extends('install.layout')

@php
    $allCompleted = true;
    $usuario = \Illuminate\Support\Facades\Auth::user();
@endphp

@section('paso')
    <section class="step step--center">
        <div class="success-icon" aria-hidden="true">✓</div>
        <h2 class="step__title">¡Bienvenida{{ $usuario ? ', Psc. ' . $usuario->nombre : '' }}!</h2>
        <p class="step__desc">Tu PsicoCMS ya está listo para usarse. Has iniciado sesión automáticamente como administradora.</p>

        <div class="actions actions--center">
            <a class="btn btn--primary" href="/panel-psicologa">Ir al panel</a>
            <a class="btn btn--ghost" href="/">Ver mi web</a>
        </div>
    </section>
@endsection
