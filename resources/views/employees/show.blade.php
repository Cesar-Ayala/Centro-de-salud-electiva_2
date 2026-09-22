@extends('layouts.app')
@section('title', 'Ficha del empleado')
@section('subtitle', $employee->name)
@section('breadcrumb', 'Empleados')

@section('actions')
    <a href="{{ route('employees.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver</a>
    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary"><x-icon name="pencil" />Editar ficha</a>
    <a href="{{ route('employee-vacations.create') }}" class="btn btn-ghost"><x-icon name="sun" />Registrar vacaciones</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
@endsection

@section('content')
    <div class="card mb-4 fade-in">
        <div class="p-5 flex flex-col md:flex-row md:items-center gap-4">
            <span class="avatar avatar-lg" style="background: linear-gradient(135deg,#1d4ed8,#0a2540)">
                {{ mb_strtoupper(mb_substr($employee->name, 0, 2)) }}
            </span>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-slate-900">{{ $employee->name }}</h2>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <span class="badge badge-info">{{ str_replace('_', ' de ', $employee->employee_type) }}</span>
                    <span class="badge badge-mute">NIF {{ $employee->nif }}</span>
                    <span class="badge badge-brand">{{ $employee->vacations->count() }} periodo(s) de vacaciones</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="card-head"><h3 class="card-title">Datos del empleado</h3></div>
            <div class="card-body space-y-3 text-sm">
                @php
                    $datos = [
                        ['id', 'NIF / Cédula', $employee->nif],
                        ['shield', 'Seguridad social', $employee->social_security_number],
                        ['phone', 'Teléfono', $employee->phone ?? '—'],
                        ['pin', 'Dirección', $employee->address ?? '—'],
                        ['building', 'Población', $employee->town ?? '—'],
                    ];
                @endphp
                @foreach ($datos as [$icono, $etiqueta, $valor])
                    <div class="flex items-start gap-3">
                        <span class="icon-chip chip-ink" style="width:32px;height:32px;border-radius:9px"><x-icon :name="$icono" class="w-4 h-4" /></span>
                        <div><p class="section-label">{{ $etiqueta }}</p><p class="text-slate-800">{{ $valor }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <h3 class="card-title">Historial de vacaciones</h3>
                <a href="{{ route('employee-vacations.index', ['employee_id' => $employee->id]) }}" class="link text-xs no-print">Gestionar</a>
            </div>
            <div class="card-body">
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse ($employee->vacations as $vacation)
                        @php
                            $tono = ['Planificadas' => 'badge-info', 'Disfrutadas' => 'badge-mute', 'Canceladas' => 'badge-bad'][$vacation->status] ?? 'badge-mute';
                            $dias = $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : null;
                        @endphp
                        <li class="py-2 flex items-center justify-between gap-2">
                            <div>
                                <p class="num text-slate-700">{{ $vacation->start_date?->format('d/m/Y') }} – {{ $vacation->end_date?->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $dias ? $dias.' días' : '' }} {{ $vacation->reason ? '· '.$vacation->reason : '' }}</p>
                            </div>
                            <span class="badge {{ $tono }}">{{ $vacation->status }}</span>
                        </li>
                    @empty
                        <li><x-empty icon="sun" title="Sin registros de vacaciones" /></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
