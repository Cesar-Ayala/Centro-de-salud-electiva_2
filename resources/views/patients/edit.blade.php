@extends('layouts.app')
@section('title', 'Editar paciente')

@section('breadcrumb', 'Pacientes')

@section('actions')
    <a href="{{ route('patients.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-4xl fade-in">
        <form method="POST" action="{{ route('patients.update', $patient) }}">
            @method('PUT')
            @include('patients._form')
        </form>
    </div>
@endsection
