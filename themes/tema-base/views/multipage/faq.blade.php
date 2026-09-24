@extends('theme::layout')

@section('titulo', 'Preguntas frecuentes · ' . ($user?->nombre ?? 'Psicología'))

@section('contenido')
    @include('theme::partials.section-faq')
    @include('theme::partials.section-cta-cita')
@endsection
