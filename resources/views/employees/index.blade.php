@extends('layouts.app')
@section('title', 'Empleados')
@section('subtitle', 'Personal no médico del centro de salud')
@section('breadcrumb', 'Empleados')

@section('actions')
    <a href="{{ route('employees.create') }}" class="btn btn-primary"><x-icon name="plus" />Nuevo empleado</a>
    <a href="{{ route('exports.download', ['resource' => 'employees'] + request()->only(['search', 'employee_type'])) }}"
       class="btn btn-ghost"><x-icon name="download" />Exportar CSV</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
    <span class="ml-auto badge badge-mute">{{ $employees->total() }} registro(s)</span>
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
                    <label class="field-label">Cargo</label>
                    <select name="employee_type" class="select">
                        <option value="">Todos los cargos</option>
                        @foreach (['ATS', 'ATS_Zona', 'Auxiliar', 'Celador', 'Administrativo'] as $type)
                            <option value="{{ $type }}" @selected(request('employee_type') === $type)>{{ str_replace('_', ' de ', $type) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-dark"><x-icon name="filter" />Filtrar</button>
                <a href="{{ route('employees.index') }}" class="btn btn-ghost">Limpiar</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th><x-sort-link field="name" label="Empleado" /></th>
                        <th><x-sort-link field="employee_type" label="Cargo" /></th>
                        <th><x-sort-link field="nif" label="NIF" /></th>
                        <th>Teléfono</th>
                        <th><x-sort-link field="vacations_count" label="Vacaciones" /></th>
                        <th class="text-right no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($employees as $employee)
                    @php
                        $cargoTono = [
                            'ATS' => 'badge-brand', 'ATS_Zona' => 'badge-brand',
                            'Auxiliar' => 'badge-info', 'Celador' => 'badge-warn', 'Administrativo' => 'badge-mute',
                        ][$employee->employee_type] ?? 'badge-mute';
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#1d4ed8,#0a2540)">
                                    {{ mb_strtoupper(mb_substr($employee->name, 0, 1)) }}
                                </span>
                                <a href="{{ route('employees.show', $employee) }}" class="font-medium text-slate-900 hover:text-teal-700">
                                    {{ $employee->name }}
                                </a>
                            </div>
                        </td>
                        <td><span class="badge {{ $cargoTono }}">{{ str_replace('_', ' de ', $employee->employee_type) }}</span></td>
                        <td class="num text-slate-600">{{ $employee->nif }}</td>
                        <td class="text-slate-600">{{ $employee->phone ?? '—' }}</td>
                        <td class="num">{{ $employee->vacations_count }}</td>
                        <td class="text-right whitespace-nowrap no-print">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-ghost" title="Ver ficha"><x-icon name="eye" /></a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-ghost" title="Editar"><x-icon name="pencil" /></a>
                                @if (auth()->user()->isAdmin())
                                    <x-delete-form :action="route('employees.destroy', $employee)"
                                                   message="Se eliminará al empleado {{ $employee->name }} de forma definitiva." />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty icon="briefcase" title="No se encontraron empleados" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col md:flex-row md:items-center gap-3 justify-between no-print">
            <x-per-page />
            <div class="pagination-wrap">{{ $employees->links() }}</div>
        </div>
    </div>
@endsection
