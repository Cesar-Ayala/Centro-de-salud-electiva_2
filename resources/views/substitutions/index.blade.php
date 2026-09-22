@extends('layouts.app')
@section('title', 'Sustituciones')
@section('subtitle', 'Reemplazos temporales entre médicos')
@section('breadcrumb', 'Sustituciones')

@section('actions')
    <a href="{{ route('substitutions.create') }}" class="btn btn-primary"><x-icon name="plus" />Nueva sustitución</a>
    <a href="{{ route('exports.download', ['resource' => 'substitutions'] + request()->only(['doctor_id', 'status'])) }}"
       class="btn btn-ghost"><x-icon name="download" />Exportar CSV</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
    <span class="ml-auto badge badge-mute">{{ $substitutions->total() }} registro(s)</span>
@endsection

@section('content')
    {{-- Accesos rápidos por estado --}}
    <div class="flex flex-wrap gap-2 mb-4 no-print">
        <a href="{{ route('substitutions.index') }}"
           class="btn btn-sm {{ request('status') ? 'btn-ghost' : 'btn-soft' }}">Todas</a>
        @foreach ($statuses as $status)
            <a href="{{ route('substitutions.index', ['status' => $status]) }}"
               class="btn btn-sm {{ request('status') === $status ? 'btn-soft' : 'btn-ghost' }}">{{ $status }}</a>
        @endforeach
    </div>

    <div class="card fade-in">
        <div class="p-4 border-b border-slate-100 no-print">
            <form method="GET" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="field-label">Médico</label>
                    <select name="doctor_id" class="select">
                        <option value="">Todos los médicos</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected((string) request('doctor_id') === (string) $doctor->id)>{{ $doctor->name }}</option>
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
                <a href="{{ route('substitutions.index') }}" class="btn btn-ghost">Limpiar</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr><th>Titular</th><th></th><th>Sustituto</th><th>Periodo</th><th>Motivo</th><th>Estado</th><th class="text-right no-print">Acciones</th></tr>
                </thead>
                <tbody>
                @forelse ($substitutions as $substitution)
                    @php
                        $tono = ['Programada' => 'badge-info', 'Activa' => 'badge-ok', 'Finalizada' => 'badge-mute'][$substitution->status] ?? 'badge-mute';
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($substitution->titularDoctor?->name ?? '?', 0, 1)) }}</span>
                                <span class="font-medium text-slate-900">{{ $substitution->titularDoctor?->name }}</span>
                            </div>
                        </td>
                        <td class="text-slate-300"><x-icon name="repeat" class="w-4 h-4" /></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#f59e0b,#b45309)">
                                    {{ mb_strtoupper(mb_substr($substitution->substituteDoctor?->name ?? '?', 0, 1)) }}
                                </span>
                                <span class="text-slate-700">{{ $substitution->substituteDoctor?->name }}</span>
                            </div>
                        </td>
                        <td class="num text-slate-500">
                            {{ $substitution->start_date?->format('d/m/Y') }} – {{ $substitution->end_date?->format('d/m/Y') }}
                        </td>
                        <td class="text-slate-500">{{ $substitution->reason ?? '—' }}</td>
                        <td><span class="badge {{ $tono }}"><span class="dot"></span>{{ $substitution->status }}</span></td>
                        <td class="text-right whitespace-nowrap no-print">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('substitutions.edit', $substitution) }}" class="btn btn-sm btn-ghost" title="Editar"><x-icon name="pencil" /></a>
                                @if (auth()->user()->isAdmin())
                                    <x-delete-form :action="route('substitutions.destroy', $substitution)"
                                                   message="Se eliminará la sustitución registrada de forma definitiva." />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty icon="repeat" title="No hay sustituciones registradas" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col md:flex-row md:items-center gap-3 justify-between no-print">
            <x-per-page />
            <div class="pagination-wrap">{{ $substitutions->links() }}</div>
        </div>
    </div>
@endsection
