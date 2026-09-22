@extends('layouts.app')
@section('title', 'Ficha del médico')
@section('subtitle', $doctor->name)
@section('breadcrumb', 'Médicos')

@section('actions')
    <a href="{{ route('doctors.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver</a>
    <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-primary"><x-icon name="pencil" />Editar ficha</a>
    <a href="{{ route('schedules.create') }}" class="btn btn-ghost"><x-icon name="calendar" />Asignar horario</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
@endsection

@section('content')
    {{-- Cabecera de la ficha --}}
    <div class="card mb-4 fade-in">
        <div class="p-5 flex flex-col md:flex-row md:items-center gap-4">
            <span class="avatar avatar-lg">{{ mb_strtoupper(mb_substr($doctor->name, 0, 2)) }}</span>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-semibold text-slate-900">{{ $doctor->name }}</h2>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <span class="badge badge-brand">{{ $doctor->doctor_type }}</span>
                    @if ($doctor->is_active)
                        <span class="badge badge-ok"><span class="dot"></span>Activo</span>
                    @else
                        <span class="badge badge-mute">De baja desde {{ $doctor->discharge_date?->format('d/m/Y') }}</span>
                    @endif
                    <span class="badge badge-info">Colegiado {{ $doctor->collegiate_number }}</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="stat-value text-teal-700">{{ $doctor->patients->count() }}</p>
                    <p class="section-label">Pacientes</p>
                </div>
                <div>
                    <p class="stat-value">{{ $schedules->count() }}</p>
                    <p class="section-label">Franjas</p>
                </div>
                <div>
                    <p class="stat-value">{{ $doctor->vacations->count() }}</p>
                    <p class="section-label">Vacaciones</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Datos personales --}}
        <div class="card">
            <div class="card-head"><h3 class="card-title">Datos de contacto</h3></div>
            <div class="card-body space-y-3 text-sm">
                @php
                    $datos = [
                        ['id', 'NIF / Cédula', $doctor->nif],
                        ['shield', 'Seguridad social', $doctor->social_security_number],
                        ['phone', 'Teléfono', $doctor->phone ?? '—'],
                        ['pin', 'Dirección', $doctor->address ?? '—'],
                        ['building', 'Población', trim(($doctor->town ?? '—').' '.($doctor->province ? '· '.$doctor->province : ''))],
                        ['calendar', 'Fecha de alta', $doctor->hiring_date?->format('d/m/Y') ?? '—'],
                    ];
                @endphp
                @foreach ($datos as [$icono, $etiqueta, $valor])
                    <div class="flex items-start gap-3">
                        <span class="icon-chip chip-ink" style="width:32px;height:32px;border-radius:9px"><x-icon :name="$icono" class="w-4 h-4" /></span>
                        <div class="min-w-0">
                            <p class="section-label">{{ $etiqueta }}</p>
                            <p class="text-slate-800 break-words">{{ $valor }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Horario semanal --}}
        <div class="card lg:col-span-2">
            <div class="card-head">
                <h3 class="card-title">Horario de consulta</h3>
                <span class="badge badge-brand">
                    {{ round($schedules->sum(function ($s) {
                        $i = strtotime((string) $s->start_time); $f = strtotime((string) $s->end_time);
                        return $f > $i ? ($f - $i) / 3600 : 0;
                    }), 1) }} h semanales
                </span>
            </div>
            <div class="card-body">
                @if ($schedules->count())
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                        @foreach (\App\Models\Schedule::DAYS as $dia)
                            @php $franjas = $schedules->where('day_of_week', $dia); @endphp
                            <div class="rounded-xl border border-slate-100 p-2">
                                <p class="section-label mb-1.5">{{ mb_substr($dia, 0, 3) }}</p>
                                @forelse ($franjas as $franja)
                                    <div class="slot mb-1">
                                        <strong>{{ substr($franja->start_time, 0, 5) }}</strong>
                                        {{ substr($franja->end_time, 0, 5) }}
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-300">Libre</p>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-empty icon="calendar" title="Sin horario asignado"
                             hint="Asigne franjas de consulta desde el módulo de horarios." />
                @endif
            </div>
        </div>

        {{-- Vacaciones --}}
        <div class="card">
            <div class="card-head"><h3 class="card-title">Vacaciones</h3></div>
            <div class="card-body">
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse ($doctor->vacations as $vacation)
                        @php
                            $tono = ['Planificadas' => 'badge-info', 'Disfrutadas' => 'badge-mute', 'Canceladas' => 'badge-bad'][$vacation->status] ?? 'badge-mute';
                        @endphp
                        <li class="py-2 flex items-center justify-between gap-2">
                            <span class="num text-slate-600">{{ $vacation->start_date?->format('d/m/Y') }} – {{ $vacation->end_date?->format('d/m/Y') }}</span>
                            <span class="badge {{ $tono }}">{{ $vacation->status }}</span>
                        </li>
                    @empty
                        <li><x-empty icon="palm" title="Sin registros" /></li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Sustituciones --}}
        <div class="card lg:col-span-2">
            <div class="card-head"><h3 class="card-title">Sustituciones</h3></div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Rol</th><th>Contraparte</th><th>Periodo</th><th>Estado</th></tr></thead>
                    <tbody>
                    @php
                        $todas = $doctor->substitutionsAsTitular->map(fn ($s) => ['Titular', $s->substituteDoctor?->name, $s])
                            ->concat($doctor->substitutionsAsSubstitute->map(fn ($s) => ['Sustituto', $s->titularDoctor?->name, $s]));
                    @endphp
                    @forelse ($todas as [$rol, $contraparte, $sustitucion])
                        @php
                            $tono = ['Programada' => 'badge-info', 'Activa' => 'badge-ok', 'Finalizada' => 'badge-mute'][$sustitucion->status] ?? 'badge-mute';
                        @endphp
                        <tr>
                            <td><span class="badge {{ $rol === 'Titular' ? 'badge-brand' : 'badge-warn' }}">{{ $rol }}</span></td>
                            <td class="text-slate-700">{{ $contraparte }}</td>
                            <td class="num text-slate-500">{{ $sustitucion->start_date?->format('d/m/Y') }} – {{ $sustitucion->end_date?->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $tono }}">{{ $sustitucion->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty icon="repeat" title="Sin sustituciones registradas" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pacientes --}}
        <div class="card lg:col-span-3">
            <div class="card-head">
                <h3 class="card-title">Pacientes asignados</h3>
                <a href="{{ route('patients.index', ['doctor_id' => $doctor->id]) }}" class="link text-xs no-print">Ver en el listado</a>
            </div>
            <div class="card-body">
                @if ($doctor->patients->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($doctor->patients as $patient)
                            <a href="{{ route('patients.show', $patient) }}"
                               class="flex items-center gap-2 p-2 rounded-lg border border-slate-100 hover:bg-slate-50">
                                <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#0d9488,#1d4ed8)">
                                    {{ mb_strtoupper(mb_substr($patient->name, 0, 1)) }}
                                </span>
                                <span class="text-sm text-slate-700 truncate">{{ $patient->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-empty icon="patient" title="Sin pacientes asignados" />
                @endif
            </div>
        </div>
    </div>
@endsection
