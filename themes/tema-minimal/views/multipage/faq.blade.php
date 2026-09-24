@extends('theme::layout')
@section('titulo', 'FAQ · ' . ($user?->nombre ?? ''))
@section('contenido')@include('theme::partials.section-faq')@endsection
