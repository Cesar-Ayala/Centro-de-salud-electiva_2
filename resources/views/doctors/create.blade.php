@extends('layouts.app')
@section('title', 'Nuevo médico')

@section('breadcrumb', 'Médicos')

@section('actions')
    <a href="{{ route('doctors.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-4xl fade-in">
        <form method="POST" action="{{ route('doctors.store') }}">
            @include('doctors._form')
        </form>
    </div>
@endsection
