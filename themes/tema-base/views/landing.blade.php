@extends('theme::layout')

@section('hero')
    @include('theme::partials.section-hero')
@endsection

@section('contenido')
    @include('theme::partials.section-terapias')
    @include('theme::partials.section-sobre-mi')
    @include('theme::partials.section-servicios')
    @include('theme::partials.section-planes')
    @include('theme::partials.section-blog')
    @include('theme::partials.section-faq')
    @include('theme::partials.section-cita')
@endsection
