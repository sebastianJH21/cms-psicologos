@extends('theme::layout')

@section('titulo', 'Pide cita · ' . ($user?->nombre ?? 'Psicología'))

@section('contenido')
    @include('theme::partials.section-cita')
@endsection
