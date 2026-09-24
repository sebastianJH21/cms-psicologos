@extends('theme::layout')

@section('titulo', 'Servicios · ' . ($user?->nombre ?? 'Psicología'))

@section('contenido')
    @include('theme::partials.section-servicios')
    @include('theme::partials.section-planes')
    @include('theme::partials.section-cta-cita')
@endsection
