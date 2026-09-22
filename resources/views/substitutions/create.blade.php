@extends('layouts.app')
@section('title', 'Nueva sustitución')

@section('breadcrumb', 'Sustituciones')

@section('actions')
    <a href="{{ route('substitutions.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver al listado</a>
@endsection

@section('content')

    <div class="card p-6 max-w-3xl fade-in">
        <form method="POST" action="{{ route('substitutions.store') }}">
            @include('substitutions._form')
        </form>
    </div>
@endsection
