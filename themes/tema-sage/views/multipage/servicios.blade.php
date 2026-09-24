@extends('theme::layout')
@section('titulo', 'Servicios · ' . ($user?->nombre ?? ''))

@section('contenido')
    @include('theme::partials.section-servicios')
    @include('theme::partials.section-terapias')
    @include('theme::partials.section-planes')
@endsection
