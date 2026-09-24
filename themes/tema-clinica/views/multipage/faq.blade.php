@extends('theme::layout')
@section('titulo', 'Preguntas frecuentes · ' . ($user?->nombre ?? ''))

@section('contenido')
    @include('theme::partials.section-faq')
@endsection
