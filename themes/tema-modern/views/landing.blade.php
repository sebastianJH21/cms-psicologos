@extends('theme::layout')

@section('contenido')
    @include('theme::partials.section-hero')
    @if ($features['servicios'] ?? true)@include('theme::partials.section-servicios')@endif
    @if ($features['sobre_mi'] ?? true)@include('theme::partials.section-sobre-mi')@endif
    @include('theme::partials.section-terapias')
    @include('theme::partials.section-planes')
    @if ($features['blog'] ?? true)@include('theme::partials.section-blog')@endif
    @if ($features['faq'] ?? true)@include('theme::partials.section-faq')@endif
    @include('theme::partials.section-cita')
@endsection
