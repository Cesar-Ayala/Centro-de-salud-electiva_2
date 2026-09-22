@extends('layouts.app')
@section('title', 'Panel principal')
@section('subtitle', 'Resumen general del centro de salud')
@section('breadcrumb', 'Panel principal')

@section('content')
    @php
        $hora = (int) now()->format('H');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
        $diasES = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $mesesES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fechaLarga = $diasES[(int) now()->format('w')].', '.now()->format('d').' de '.$mesesES[(int) now()->format('n') - 1].' de '.now()->format('Y');
        $cobertura = $totalDoctors > 0 ? round(($doctorsWithSchedule / $totalDoctors) * 100) : 0;
        $asignados = $totalPatients > 0 ? round((($totalPatients - $patientsWithoutDoctor) / $totalPatients) * 100) : 0;
    @endphp

    {{-- Encabezado de bienvenida --}}
    <div class="card mb-4 overflow-hidden fade-in">
        <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-4"
             style="background: linear-gradient(120deg, #0a2540 0%, #0f3f4d 55%, #0f766e 100%);">
            <div class="flex-1 text-white">
                <p class="text-[.7rem] tracking-[.14em] uppercase text-teal-200/80">{{ $fechaLarga }}</p>
                <h2 class="text-xl md:text-2xl font-semibold mt-1">{{ $saludo }}, {{ explode(' ', auth()->user()->name)[0] }}</h2>
                <p class="text-sm text-slate-300 mt-1">
                    Hoy hay {{ $activeSubstitutions }} sustitución(es) activa(s) y {{ $absentToday->count() }} médico(s) ausente(s).
                </p>
            </div>
            <div class="flex gap-2 flex-wrap no-print">
                <a href="{{ route('reports.index') }}" class="btn btn-soft"><x-icon name="chart" />Ver reportes</a>
                <a href="{{ route('schedules.index') }}" class="btn btn-ghost"><x-icon name="calendar" />Horarios</a>
            </div>
        </div>
    </div>

    {{-- Avisos operativos reales --}}
    @if ($uncoveredToday->count() > 0)
        <div class="alert alert-warn mb-4 fade-in">
            <x-icon name="warning" />
            <div class="flex-1">
                <p class="font-semibold mb-1">{{ $uncoveredToday->count() }} médico(s) ausente(s) hoy sin sustituto asignado</p>
                <ul class="space-y-0.5">
                    @foreach ($uncoveredToday as $vacation)
                        <li>
                            {{ $vacation->doctor?->name }} — regresa el {{ $vacation->end_date?->format('d/m/Y') }}
                            <a href="{{ route('substitutions.create') }}" class="link ml-1">Asignar sustituto</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Indicadores principales --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4">
        <x-stat label="Médicos registrados" :value="$totalDoctors" icon="stethoscope" tone="brand"
                :hint="$activeDoctors.' activos · '.($totalDoctors - $activeDoctors).' de baja'"
                :href="route('doctors.index')" />
        <x-stat label="Empleados" :value="$totalEmployees" icon="briefcase" tone="ink"
                hint="Personal no médico" :href="route('employees.index')" />
        <x-stat label="Pacientes" :value="$totalPatients" icon="patient" tone="info"
                :hint="$asignados.'% con médico asignado'" :progress="$asignados"
                :href="route('patients.index')" />
        <x-stat label="Franjas de horario" :value="$totalSchedules" icon="calendar" tone="brand"
                :hint="$cobertura.'% de médicos con agenda'" :progress="$cobertura"
                :href="route('schedules.index')" />
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4">
        <x-stat label="Sustituciones activas" :value="$activeSubstitutions" icon="repeat" tone="warn"
                hint="En curso hoy" :href="route('substitutions.index')" />
        <x-stat label="Ausentes hoy" :value="$absentToday->count()" icon="palm"
                :tone="$uncoveredToday->count() ? 'bad' : 'brand'"
                :hint="$coveredCount.' con sustituto'" :href="route('doctor-vacations.index')" />
        <x-stat label="Vacaciones médicos" :value="$plannedDoctorVacations" icon="palm" tone="info"
                hint="Planificadas" :href="route('doctor-vacations.index')" />
        <x-stat label="Vacaciones empleados" :value="$plannedEmployeeVacations" icon="sun" tone="info"
                hint="Planificadas" :href="route('employee-vacations.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Distribución de médicos por tipo (anillo) --}}
        @php
            $total = max(1, $doctorsByType->sum());
            $palette = ['#0f766e', '#2563eb', '#f59e0b', '#8b5cf6', '#ef4444'];
            $offset = 0; $i = 0;
        @endphp
        <div class="card">
            <div class="card-head"><h2 class="card-title">Médicos por tipo</h2>
                <span class="badge badge-mute">{{ $doctorsByType->sum() }} total</span></div>
            <div class="card-body">
                @if ($doctorsByType->sum() > 0)
                    <div class="flex items-center gap-5">
                        <svg viewBox="0 0 120 120" class="w-28 h-28 flex-none" style="transform: rotate(-90deg)">
                            <circle cx="60" cy="60" r="42" fill="none" stroke="#eef2f7" stroke-width="16"/>
                            @foreach ($doctorsByType as $type => $count)
                                @php
                                    $fraction = $count / $total;
                                    $length = $fraction * 263.9;
                                    $color = $palette[$i % count($palette)];
                                @endphp
                                <circle cx="60" cy="60" r="42" fill="none" stroke="{{ $color }}" stroke-width="16"
                                        stroke-dasharray="{{ round($length, 2) }} 263.9"
                                        stroke-dashoffset="-{{ round($offset, 2) }}" stroke-linecap="butt"/>
                                @php $offset += $length; $i++; @endphp
                            @endforeach
                        </svg>
                        <ul class="flex-1 space-y-2 text-sm">
                            @php $j = 0; @endphp
                            @foreach ($doctorsByType as $type => $count)
                                <li class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full flex-none" style="background: {{ $palette[$j % count($palette)] }}"></span>
                                    <span class="flex-1 text-slate-600">{{ $type }}</span>
                                    <span class="font-semibold text-slate-900">{{ $count }}</span>
                                    <span class="text-xs text-slate-400 w-10 text-right">{{ round($count / $total * 100) }}%</span>
                                </li>
                                @php $j++; @endphp
                            @endforeach
                        </ul>
                    </div>
                @else
                    <x-empty icon="stethoscope" title="Todavía no hay médicos registrados"
                             hint="Registre el primer médico para ver la distribución." />
                @endif
            </div>
        </div>

        {{-- Carga semanal de consulta --}}
        @php $maxLoad = max(1, $weeklyLoad->max()); @endphp
        <div class="card lg:col-span-2">
            <div class="card-head">
                <h2 class="card-title">Horas de consulta por día</h2>
                <span class="badge badge-brand">{{ $weeklyLoad->sum() }} h semanales</span>
            </div>
            <div class="card-body">
                <div class="flex items-end gap-2 md:gap-4 h-40">
                    @foreach ($weeklyLoad as $day => $hours)
                        <div class="flex-1 flex flex-col items-center justify-end h-full gap-2">
                            <span class="text-xs font-semibold text-slate-700">{{ $hours > 0 ? $hours.'h' : '' }}</span>
                            <div class="w-full rounded-t-lg transition-all"
                                 style="height: {{ max(3, round($hours / $maxLoad * 100)) }}%;
                                        background: linear-gradient(180deg, var(--brand-500), var(--brand-700));
                                        opacity: {{ $hours > 0 ? 1 : .25 }}"></div>
                            <span class="text-[.68rem] text-slate-500">{{ mb_substr($day, 0, 3) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Últimas sustituciones --}}
        <div class="card lg:col-span-2">
            <div class="card-head">
                <h2 class="card-title">Últimas sustituciones</h2>
                <a href="{{ route('substitutions.index') }}" class="link text-xs">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Titular</th><th>Sustituto</th><th>Periodo</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($latestSubstitutions as $substitution)
                        @php
                            $tono = ['Activa' => 'badge-ok', 'Programada' => 'badge-info', 'Finalizada' => 'badge-mute'][$substitution->status] ?? 'badge-mute';
                        @endphp
                        <tr>
                            <td class="font-medium text-slate-900">{{ $substitution->titularDoctor?->name }}</td>
                            <td>{{ $substitution->substituteDoctor?->name }}</td>
                            <td class="num text-slate-500">{{ $substitution->start_date?->format('d/m/Y') }} – {{ $substitution->end_date?->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $tono }}"><span class="dot"></span>{{ $substitution->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty icon="repeat" title="Sin sustituciones registradas" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Médicos con más pacientes --}}
        <div class="card">
            <div class="card-head"><h2 class="card-title">Médicos con más pacientes</h2></div>
            <div class="card-body space-y-3">
                @php $maxPacientes = max(1, $topDoctors->max('patients_count') ?? 1); @endphp
                @forelse ($topDoctors as $doctor)
                    <a href="{{ route('doctors.show', $doctor) }}" class="block group">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($doctor->name, 0, 1)) }}</span>
                            <span class="flex-1 truncate text-slate-700 group-hover:text-teal-700">{{ $doctor->name }}</span>
                            <span class="font-semibold text-slate-900">{{ $doctor->patients_count }}</span>
                        </div>
                        <div class="progress mt-1.5"><span style="width: {{ round($doctor->patients_count / $maxPacientes * 100) }}%"></span></div>
                    </a>
                @empty
                    <x-empty icon="patient" title="Sin datos de pacientes" />
                @endforelse
            </div>
        </div>

        {{-- Próximas vacaciones --}}
        <div class="card lg:col-span-3">
            <div class="card-head">
                <h2 class="card-title">Próximas vacaciones de médicos</h2>
                <a href="{{ route('doctor-vacations.index') }}" class="link text-xs">Gestionar</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Médico</th><th>Desde</th><th>Hasta</th><th>Días</th><th>Motivo</th><th>Faltan</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($upcomingVacations as $vacation)
                        @php
                            $dias = $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : null;
                            $faltan = $vacation->start_date ? now()->startOfDay()->diffInDays($vacation->start_date, false) : null;
                        @endphp
                        <tr>
                            <td class="font-medium text-slate-900">{{ $vacation->doctor?->name }}</td>
                            <td class="num">{{ $vacation->start_date?->format('d/m/Y') }}</td>
                            <td class="num">{{ $vacation->end_date?->format('d/m/Y') }}</td>
                            <td class="num">{{ $dias ?? '—' }}</td>
                            <td class="text-slate-500">{{ $vacation->reason ?? '—' }}</td>
                            <td>
                                @if (is_null($faltan))
                                    —
                                @elseif ($faltan > 0)
                                    <span class="badge badge-info">{{ (int) $faltan }} días</span>
                                @else
                                    <span class="badge badge-warn">En curso</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty icon="palm" title="Sin vacaciones planificadas" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
