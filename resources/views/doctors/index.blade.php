@extends('layouts.app')
@section('title', 'Médicos')
@section('subtitle', 'Registro de médicos titulares, interinos y sustitutos')
@section('breadcrumb', 'Médicos')

@section('actions')
    <a href="{{ route('doctors.create') }}" class="btn btn-primary"><x-icon name="plus" />Nuevo médico</a>
    <a href="{{ route('exports.download', ['resource' => 'doctors'] + request()->only(['search', 'doctor_type', 'status'])) }}"
       class="btn btn-ghost"><x-icon name="download" />Exportar CSV</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
    <span class="ml-auto badge badge-mute">{{ $doctors->total() }} registro(s)</span>
@endsection

@section('content')
    <div class="card fade-in">
        {{-- Filtros --}}
        <div class="p-4 border-b border-slate-100 no-print">
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <div class="min-w-[14rem] flex-1">
                    <label class="field-label">Buscar</label>
                    <div class="input-icon">
                        <x-icon name="search" />
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nombre, NIF o número de colegiado" class="input">
                    </div>
                </div>
                <div>
                    <label class="field-label">Tipo</label>
                    <select name="doctor_type" class="select">
                        <option value="">Todos los tipos</option>
                        @foreach (['Titular', 'Interino', 'Sustituto'] as $type)
                            <option value="{{ $type }}" @selected(request('doctor_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Estado</label>
                    <select name="status" class="select">
                        <option value="">Todos</option>
                        <option value="activos" @selected(request('status') === 'activos')>Solo activos</option>
                        <option value="baja" @selected(request('status') === 'baja')>Solo de baja</option>
                    </select>
                </div>
                <button class="btn btn-dark"><x-icon name="filter" />Filtrar</button>
                <a href="{{ route('doctors.index') }}" class="btn btn-ghost">Limpiar</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th><x-sort-link field="name" label="Médico" /></th>
                        <th><x-sort-link field="doctor_type" label="Tipo" /></th>
                        <th><x-sort-link field="nif" label="NIF" /></th>
                        <th><x-sort-link field="collegiate_number" label="Colegiado" /></th>
                        <th><x-sort-link field="patients_count" label="Pacientes" /></th>
                        <th>Situación</th>
                        <th class="text-right no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($doctors as $doctor)
                    @php
                        $tipoTono = ['Titular' => 'badge-brand', 'Interino' => 'badge-info', 'Sustituto' => 'badge-warn'][$doctor->doctor_type] ?? 'badge-mute';
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($doctor->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="font-medium text-slate-900 hover:text-teal-700 block truncate">
                                        {{ $doctor->name }}
                                    </a>
                                    <span class="text-xs text-slate-400">{{ $doctor->phone ?? 'Sin teléfono' }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge {{ $tipoTono }}">{{ $doctor->doctor_type }}</span></td>
                        <td class="num text-slate-600">{{ $doctor->nif }}</td>
                        <td class="num text-slate-600">{{ $doctor->collegiate_number }}</td>
                        <td class="num">
                            <span class="font-semibold text-slate-900">{{ $doctor->patients_count }}</span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @if ($doctor->is_active)
                                    <span class="badge badge-ok"><span class="dot"></span>Activo</span>
                                @else
                                    <span class="badge badge-mute">De baja</span>
                                @endif
                                @if (in_array($doctor->id, $onVacation, true))
                                    <span class="badge badge-warn">De vacaciones</span>
                                @endif
                                @if (in_array($doctor->id, $substituted, true))
                                    <span class="badge badge-info">Sustituido</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-right whitespace-nowrap no-print">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-ghost" title="Ver ficha"><x-icon name="eye" /></a>
                                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-ghost" title="Editar"><x-icon name="pencil" /></a>
                                @if (auth()->user()->isAdmin())
                                    <x-delete-form :action="route('doctors.destroy', $doctor)"
                                                   message="Se eliminará al médico {{ $doctor->name }} de forma definitiva." />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <x-empty icon="stethoscope" title="No se encontraron médicos"
                                 hint="Ajuste los filtros o registre un nuevo médico." />
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col md:flex-row md:items-center gap-3 justify-between no-print">
            <x-per-page />
            <div class="pagination-wrap">{{ $doctors->links() }}</div>
        </div>
    </div>
@endsection
