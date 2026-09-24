@extends('theme::layout')
@section('titulo', 'Sobre mí · ' . ($user?->nombre ?? ''))
@section('contenido')
    @include('theme::partials.section-sobre-mi')
    @include('theme::partials.section-terapias')
@endsection
