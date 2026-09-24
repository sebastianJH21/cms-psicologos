@extends('theme::layout')

@section('titulo', 'Sobre mí · ' . ($user?->nombre ?? 'Psicología'))

@section('contenido')
    @include('theme::partials.section-sobre-mi')
    @include('theme::partials.section-terapias')
    @include('theme::partials.section-cta-cita')
@endsection
