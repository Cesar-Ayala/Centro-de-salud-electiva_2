@extends('layouts.app')
@section('title', 'Nuevo horario')

@section('breadcrumb', 'Horarios')

@section('actions')
    <a href="{{ route('schedules.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-3xl fade-in">
        <form method="POST" action="{{ route('schedules.store') }}">
            @include('schedules._form')
        </form>
    </div>
@endsection
