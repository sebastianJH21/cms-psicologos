@extends('theme::layout')
@section('contenido')
    @include('theme::partials.section-hero')
    @if ($features['servicios'] ?? true)@include('theme::partials.section-servicios')@endif
    @if ($features['sobre_mi'] ?? true)@include('theme::partials.section-sobre-mi')@endif
    @include('theme::partials.section-planes')
@endsection
