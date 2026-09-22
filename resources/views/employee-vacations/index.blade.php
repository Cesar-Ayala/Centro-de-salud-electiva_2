@extends('layouts.app')
@section('title', 'Vacaciones de empleados')
@section('subtitle', 'Planificación y seguimiento de periodos vacacionales')
@section('breadcrumb', 'Vacaciones de empleados')

@section('actions')
    <a href="{{ route('employee-vacations.create') }}" class="btn btn-primary"><x-icon name="plus" />Registrar vacaciones</a>
    <a href="{{ route('exports.download', ['resource' => 'employee-vacations'] + request()->only(['employee_id', 'status'])) }}"
       class="btn btn-ghost"><x-icon name="download" />Exportar CSV</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
    <span class="ml-auto badge badge-mute">{{ $vacations->total() }} registro(s)</span>
@endsection

@section('content')
    <div class="flex flex-wrap gap-2 mb-4 no-print">
        <a href="{{ route('employee-vacations.index') }}" class="btn btn-sm {{ request('status') ? 'btn-ghost' : 'btn-soft' }}">Todos</a>
        @foreach ($statuses as $status)
            <a href="{{ route('employee-vacations.index', ['status' => $status]) }}"
               class="btn btn-sm {{ request('status') === $status ? 'btn-soft' : 'btn-ghost' }}">{{ $status }}</a>
        @endforeach
    </div>

    <div class="card fade-in">
        <div class="p-4 border-b border-slate-100 no-print">
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="field-label">Empleado</label>
                    <select name="employee_id" class="select">
                        <option value="">Todos</option>
                        @foreach ($employees as $item)
                            <option value="{{ $item->id }}" @selected((string) request('employee_id') === (string) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Estado</label>
                    <select name="status" class="select">
                        <option value="">Todos los estados</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-dark"><x-icon name="filter" />Filtrar</button>
                <a href="{{ route('employee-vacations.index') }}" class="btn btn-ghost">Limpiar</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr><th>Empleado</th><th>Periodo</th><th>Días</th><th>Situación</th><th>Motivo</th><th>Estado</th><th class="text-right no-print">Acciones</th></tr>
                </thead>
                <tbody>
                @forelse ($vacations as $vacation)
                    @php
                        $persona = $vacation->employee;
                        $dias = $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : null;
                        $hoy = now()->startOfDay();
                        $enCurso = $vacation->start_date && $vacation->end_date
                            && $vacation->start_date->lte($hoy) && $vacation->end_date->gte($hoy);
                        $futura = $vacation->start_date && $vacation->start_date->gt($hoy);
                        $tono = ['Planificadas' => 'badge-info', 'Disfrutadas' => 'badge-mute', 'Canceladas' => 'badge-bad'][$vacation->status] ?? 'badge-mute';
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($persona?->name ?? '?', 0, 1)) }}</span>
                                <span class="font-medium text-slate-900">{{ $persona?->name }}</span>
                            </div>
                        </td>
                        <td class="num text-slate-500">{{ $vacation->start_date?->format('d/m/Y') }} – {{ $vacation->end_date?->format('d/m/Y') }}</td>
                        <td class="num">{{ $dias ?? '—' }}</td>
                        <td>
                            @if ($enCurso)
                                <span class="badge badge-warn"><span class="dot"></span>En curso</span>
                            @elseif ($futura)
                                <span class="badge badge-info">En {{ (int) $hoy->diffInDays($vacation->start_date) }} días</span>
                            @else
                                <span class="badge badge-mute">Finalizadas</span>
                            @endif
                        </td>
                        <td class="text-slate-500">{{ $vacation->reason ?? '—' }}</td>
                        <td><span class="badge {{ $tono }}">{{ $vacation->status }}</span></td>
                        <td class="text-right whitespace-nowrap no-print">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('employee-vacations.edit', $vacation) }}" class="btn btn-sm btn-ghost" title="Editar"><x-icon name="pencil" /></a>
                                @if (auth()->user()->isAdmin())
                                    <x-delete-form :action="route('employee-vacations.destroy', $vacation)"
                                                   message="Se eliminará este periodo de vacaciones de forma definitiva." />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty icon="sun" title="No hay vacaciones registradas" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col md:flex-row md:items-center gap-3 justify-between no-print">
            <x-per-page />
            <div class="pagination-wrap">{{ $vacations->links() }}</div>
        </div>
    </div>
@endsection
