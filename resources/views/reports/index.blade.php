@extends('layouts.app')
@section('title', 'Reportes')
@section('subtitle', 'Consultas de apoyo a la gestión administrativa')
@section('breadcrumb', 'Reportes')

@section('actions')
    <button type="button" onclick="window.print()" class="btn btn-dark"><x-icon name="printer" />Imprimir reporte</button>
    <a href="{{ route('exports.download', ['resource' => 'substitutions', 'status' => 'Activa']) }}" class="btn btn-ghost">
        <x-icon name="download" />Sustituciones activas (CSV)
    </a>
    <a href="{{ route('exports.download', ['resource' => 'employee-vacations', 'status' => 'Planificadas']) }}" class="btn btn-ghost">
        <x-icon name="download" />Vacaciones planificadas (CSV)
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4">
        <x-stat label="Sustituciones activas" :value="$activeSubstitutions->count()" icon="repeat" tone="warn" hint="Consulta 1" />
        <x-stat label="Médicos en el sistema" :value="$doctors->count()" icon="stethoscope" tone="brand" hint="Consultas 2 y 3" />
        <x-stat label="Vacaciones médicos" :value="$plannedDoctorVacations->count()" icon="palm" tone="info" hint="Planificadas" />
        <x-stat label="Vacaciones empleados" :value="$plannedEmployeeVacations->count()" icon="sun" tone="info" hint="Consulta 4" />
    </div>

    <div class="space-y-4">

        {{-- Reporte 1: sustituciones activas --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">1 · Médicos en sustitución activa</h2>
                    <p class="text-xs text-slate-500">Titulares ausentes cuyo reemplazo está vigente hoy</p>
                </div>
                <a href="{{ route('reports.active-substitutions') }}" target="_blank" class="link text-xs no-print">Ver en JSON</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Titular</th><th>Sustituto</th><th>Periodo</th><th>Motivo</th></tr></thead>
                    <tbody>
                    @forelse ($activeSubstitutions as $substitution)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $substitution->titularDoctor?->name }}</td>
                            <td>{{ $substitution->substituteDoctor?->name }}</td>
                            <td class="num text-slate-500">{{ $substitution->start_date?->format('d/m/Y') }} – {{ $substitution->end_date?->format('d/m/Y') }}</td>
                            <td class="text-slate-500">{{ $substitution->reason ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty icon="repeat" title="No hay sustituciones activas" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Reportes 2 y 3: por médico --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">2 y 3 · Consulta por médico</h2>
                    <p class="text-xs text-slate-500">Horario semanal y pacientes asignados</p>
                </div>
                @if ($doctorId)
                    <div class="flex gap-3 text-xs no-print">
                        <a href="{{ route('reports.doctor-schedule', $doctorId) }}" target="_blank" class="link">Horario en JSON</a>
                        <a href="{{ route('reports.doctor-patients', $doctorId) }}" target="_blank" class="link">Pacientes en JSON</a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <form method="GET" class="flex flex-wrap items-end gap-2 mb-4 no-print">
                    <div class="min-w-[16rem]">
                        <label class="field-label">Médico</label>
                        <select name="doctor_id" class="select" onchange="this.form.submit()">
                            <option value="">Seleccione un médico…</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected((string) $doctorId === (string) $doctor->id)>{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-dark"><x-icon name="search" />Consultar</button>
                </form>

                @if ($doctorId && $selectedDoctor)
                    <div class="flex flex-wrap items-center gap-3 p-3 rounded-xl mb-4" style="background: var(--brand-50)">
                        <span class="avatar">{{ mb_strtoupper(mb_substr($selectedDoctor->name, 0, 1)) }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900">{{ $selectedDoctor->name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $selectedDoctor->doctor_type }} · Colegiado {{ $selectedDoctor->collegiate_number }}
                            </p>
                        </div>
                        <span class="badge badge-brand">{{ $weeklyHours }} h semanales</span>
                        <span class="badge badge-info">{{ $patients->count() }} pacientes</span>
                        <a href="{{ route('doctors.show', $selectedDoctor) }}" class="btn btn-sm btn-ghost no-print">Ver ficha</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="section-label mb-2">Horario semanal</p>
                            <table class="table">
                                <tbody>
                                @forelse ($schedule as $row)
                                    <tr>
                                        <td><span class="badge badge-mute">{{ $row->day_of_week }}</span></td>
                                        <td class="text-right num">{{ substr($row->start_time, 0, 5) }} – {{ substr($row->end_time, 0, 5) }}</td>
                                    </tr>
                                @empty
                                    <tr><td><x-empty icon="calendar" title="Sin horario asignado" /></td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <p class="section-label mb-2">Pacientes asignados ({{ $patients->count() }})</p>
                            <ul class="divide-y divide-slate-100">
                                @forelse ($patients as $patient)
                                    <li class="py-2 flex items-center gap-2 text-sm">
                                        <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#0d9488,#1d4ed8)">
                                            {{ mb_strtoupper(mb_substr($patient->name, 0, 1)) }}
                                        </span>
                                        <a href="{{ route('patients.show', $patient) }}" class="flex-1 hover:text-teal-700">{{ $patient->name }}</a>
                                        <span class="text-slate-400 num text-xs">{{ $patient->nif }}</span>
                                    </li>
                                @empty
                                    <li><x-empty icon="patient" title="Sin pacientes asignados" /></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @else
                    <x-empty icon="stethoscope" title="Seleccione un médico"
                             hint="Verá su horario de consulta y el listado de pacientes a su cargo." />
                @endif
            </div>
        </div>

        {{-- Reporte 4: vacaciones planificadas --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">Vacaciones planificadas · médicos</h2>
                    <a href="{{ route('exports.download', ['resource' => 'doctor-vacations', 'status' => 'Planificadas']) }}"
                       class="link text-xs no-print">CSV</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Médico</th><th>Periodo</th><th>Días</th></tr></thead>
                        <tbody>
                        @forelse ($plannedDoctorVacations as $vacation)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $vacation->doctor?->name }}</td>
                                <td class="num text-slate-500">{{ $vacation->start_date?->format('d/m/Y') }} – {{ $vacation->end_date?->format('d/m/Y') }}</td>
                                <td class="num">{{ $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><x-empty icon="palm" title="Sin vacaciones planificadas" /></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">4 · Vacaciones planificadas · empleados</h2>
                    <a href="{{ route('reports.planned-vacations') }}" target="_blank" class="link text-xs no-print">Ver en JSON</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Empleado</th><th>Periodo</th><th>Días</th></tr></thead>
                        <tbody>
                        @forelse ($plannedEmployeeVacations as $vacation)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $vacation->employee?->name }}</td>
                                <td class="num text-slate-500">{{ $vacation->start_date?->format('d/m/Y') }} – {{ $vacation->end_date?->format('d/m/Y') }}</td>
                                <td class="num">{{ $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><x-empty icon="sun" title="Sin vacaciones planificadas" /></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
