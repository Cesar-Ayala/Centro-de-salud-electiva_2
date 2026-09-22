@extends('layouts.app')
@section('title', 'Búsqueda global')
@section('subtitle', 'Consulte médicos, empleados y pacientes desde un solo lugar')
@section('breadcrumb', 'Búsqueda')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('search') }}" class="flex flex-wrap gap-2">
                <div class="input-icon flex-1 min-w-[16rem]">
                    <x-icon name="search" />
                    <input type="text" name="q" value="{{ $term }}" autofocus
                           placeholder="Nombre, NIF, número de colegiado o teléfono…" class="input">
                </div>
                <button class="btn btn-primary"><x-icon name="search" />Buscar</button>
            </form>
            <p class="text-xs text-slate-500 mt-2">
                Escriba al menos 2 caracteres. La búsqueda recorre los tres registros del sistema a la vez.
            </p>
        </div>
    </div>

    @if ($term === '')
        <x-empty icon="search" title="Escriba un término para comenzar"
                 hint="Por ejemplo el apellido de un paciente o el NIF de un médico." />
    @elseif ($total === 0)
        <x-empty icon="inbox" title="Sin coincidencias para «{{ $term }}»"
                 hint="Revise la ortografía o pruebe con un término más corto." />
    @else
        <p class="text-sm text-slate-500 mb-3">
            <strong class="text-slate-900">{{ $total }}</strong> coincidencia(s) para «{{ $term }}»
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <div class="card">
                <div class="card-head">
                    <h2 class="card-title flex items-center gap-2"><span class="icon-chip chip-brand" style="width:28px;height:28px;border-radius:9px"><x-icon name="stethoscope" class="w-4 h-4" /></span>Médicos</h2>
                    <span class="badge badge-mute">{{ $doctors->count() }}</span>
                </div>
                <div class="card-body space-y-2">
                    @forelse ($doctors as $doctor)
                        <a href="{{ route('doctors.show', $doctor) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
                            <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($doctor->name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $doctor->name }}</p>
                                <p class="text-xs text-slate-500">{{ $doctor->doctor_type }} · NIF {{ $doctor->nif }}</p>
                            </div>
                            <span class="badge badge-mute">{{ $doctor->patients_count }} pac.</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400 py-2">Sin resultados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h2 class="card-title flex items-center gap-2"><span class="icon-chip chip-ink" style="width:28px;height:28px;border-radius:9px"><x-icon name="briefcase" class="w-4 h-4" /></span>Empleados</h2>
                    <span class="badge badge-mute">{{ $employees->count() }}</span>
                </div>
                <div class="card-body space-y-2">
                    @forelse ($employees as $employee)
                        <a href="{{ route('employees.show', $employee) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
                            <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#1d4ed8,#0a2540)">
                                {{ mb_strtoupper(mb_substr($employee->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $employee->name }}</p>
                                <p class="text-xs text-slate-500">{{ str_replace('_', ' de ', $employee->employee_type) }} · NIF {{ $employee->nif }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400 py-2">Sin resultados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h2 class="card-title flex items-center gap-2"><span class="icon-chip chip-info" style="width:28px;height:28px;border-radius:9px"><x-icon name="patient" class="w-4 h-4" /></span>Pacientes</h2>
                    <span class="badge badge-mute">{{ $patients->count() }}</span>
                </div>
                <div class="card-body space-y-2">
                    @forelse ($patients as $patient)
                        <a href="{{ route('patients.show', $patient) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
                            <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#0d9488,#1d4ed8)">
                                {{ mb_strtoupper(mb_substr($patient->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $patient->name }}</p>
                                <p class="text-xs text-slate-500">NIF {{ $patient->nif }} · {{ $patient->doctor?->name ?? 'Sin médico' }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400 py-2">Sin resultados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
@endsection
