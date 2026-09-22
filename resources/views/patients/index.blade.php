@extends('layouts.app')
@section('title', 'Pacientes')
@section('subtitle', 'Registro de pacientes y médico asignado')
@section('breadcrumb', 'Pacientes')

@section('actions')
    <a href="{{ route('patients.create') }}" class="btn btn-primary"><x-icon name="plus" />Nuevo paciente</a>
    <a href="{{ route('exports.download', ['resource' => 'patients'] + request()->only(['search', 'doctor_id', 'unassigned'])) }}"
       class="btn btn-ghost"><x-icon name="download" />Exportar CSV</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
    <span class="ml-auto badge badge-mute">{{ $patients->total() }} registro(s)</span>
@endsection

@section('content')
    <div class="card fade-in">
        <div class="p-4 border-b border-slate-100 no-print">
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <div class="min-w-[14rem] flex-1">
                    <label class="field-label">Buscar</label>
                    <div class="input-icon">
                        <x-icon name="search" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o NIF" class="input">
                    </div>
                </div>
                <div>
                    <label class="field-label">Médico asignado</label>
                    <select name="doctor_id" class="select">
                        <option value="">Todos los médicos</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected((string) request('doctor_id') === (string) $doctor->id)>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="btn btn-ghost">
                    <input type="checkbox" name="unassigned" value="1" @checked(request('unassigned')) onchange="this.form.submit()">
                    Solo sin asignar
                </label>
                <button class="btn btn-dark"><x-icon name="filter" />Filtrar</button>
                <a href="{{ route('patients.index') }}" class="btn btn-ghost">Limpiar</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th><x-sort-link field="name" label="Paciente" /></th>
                        <th><x-sort-link field="nif" label="NIF" /></th>
                        <th><x-sort-link field="phone" label="Teléfono" /></th>
                        <th>Médico asignado</th>
                        <th class="text-right no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($patients as $patient)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#0d9488,#1d4ed8)">
                                    {{ mb_strtoupper(mb_substr($patient->name, 0, 1)) }}
                                </span>
                                <a href="{{ route('patients.show', $patient) }}" class="font-medium text-slate-900 hover:text-teal-700">
                                    {{ $patient->name }}
                                </a>
                            </div>
                        </td>
                        <td class="num text-slate-600">{{ $patient->nif }}</td>
                        <td class="text-slate-600">{{ $patient->phone ?? '—' }}</td>
                        <td>
                            @if ($patient->doctor)
                                <a href="{{ route('doctors.show', $patient->doctor) }}" class="badge badge-brand">
                                    {{ $patient->doctor->name }}
                                </a>
                            @else
                                <span class="badge badge-warn">Sin asignar</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap no-print">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-ghost" title="Ver ficha"><x-icon name="eye" /></a>
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-ghost" title="Editar"><x-icon name="pencil" /></a>
                                @if (auth()->user()->isAdmin())
                                    <x-delete-form :action="route('patients.destroy', $patient)"
                                                   message="Se eliminará al paciente {{ $patient->name }} de forma definitiva." />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty icon="patient" title="No se encontraron pacientes" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col md:flex-row md:items-center gap-3 justify-between no-print">
            <x-per-page />
            <div class="pagination-wrap">{{ $patients->links() }}</div>
        </div>
    </div>
@endsection
