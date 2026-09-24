@extends('theme::layout')
@section('titulo', 'Pedir cita · ' . ($user?->nombre ?? ''))
@section('contenido')@include('theme::partials.section-cita')@endsection
