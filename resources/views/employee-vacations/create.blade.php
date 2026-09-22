@extends('layouts.app')
@section('title', 'Registrar vacaciones de empleado')

@section('breadcrumb', 'Vacaciones de empleados')

@section('actions')
    <a href="{{ route('employee-vacations.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-3xl fade-in">
        <form method="POST" action="{{ route('employee-vacations.store') }}">
            @include('employee-vacations._form')
        </form>
    </div>
@endsection
