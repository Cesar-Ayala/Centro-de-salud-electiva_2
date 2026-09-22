@extends('layouts.app')
@section('title', 'Registrar vacaciones de médico')

@section('breadcrumb', 'Vacaciones de médicos')

@section('actions')
    <a href="{{ route('doctor-vacations.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-3xl fade-in">
        <form method="POST" action="{{ route('doctor-vacations.store') }}">
            @include('doctor-vacations._form')
        </form>
    </div>
@endsection
