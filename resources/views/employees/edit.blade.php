@extends('layouts.app')
@section('title', 'Editar empleado')

@section('breadcrumb', 'Empleados')

@section('actions')
    <a href="{{ route('employees.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-4xl fade-in">
        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @method('PUT')
            @include('employees._form')
        </form>
    </div>
@endsection
