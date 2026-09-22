@extends('layouts.app')
@section('title', 'Mi panel')
@section('subtitle', 'Horario, sustituciones y vacaciones')
@section('breadcrumb', 'Mi panel')

@section('content')
    @if (! $doctor)
        <div class="card">
            <x-empty icon="stethoscope" title="Su cuenta no está vinculada a una ficha médica"
                     hint="Solicite al administrador que asocie su usuario con su ficha de médico." />
        </div>
    @else
        @php
            $horasSemana = round($schedules->sum(function ($s) {
                $i = strtotime((string) $s->start_time); $f = strtotime((string) $s->end_time);
                return $f > $i ? ($f - $i) / 3600 : 0;
            }), 1);
            $hoy = \App\Models\Schedule::DAYS[(int) now()->format('N') - 1] ?? null;
            $consultaHoy = $schedules->where('day_of_week', $hoy);
            $sustitucionActiva = $substitutions->firstWhere('status', 'Activa');
        @endphp

        {{-- Cabecera --}}
        <div class="card mb-4 overflow-hidden fade-in">
            <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-4"
                 style="background: linear-gradient(120deg, #0a2540 0%, #0f3f4d 60%, #0f766e 100%);">
                <span class="avatar avatar-lg" style="background: rgba(255,255,255,.14)">
                    {{ mb_strtoupper(mb_substr($doctor->name, 0, 2)) }}
                </span>
                <div class="flex-1 text-white">
                    <h2 class="text-xl font-semibold">{{ $doctor->name }}</h2>
                    <p class="text-sm text-slate-300 mt-1">
                        {{ $doctor->doctor_type }} · Colegiado {{ $doctor->collegiate_number }}
                    </p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if ($doctor->is_active)
                            <span class="badge badge-ok"><span class="dot"></span>Activo</span>
                        @else
                            <span class="badge badge-mute">De baja</span>
                        @endif
                        @if ($consultaHoy->count())
                            <span class="badge badge-brand">Hoy atiende {{ $consultaHoy->count() }} franja(s)</span>
                        @else
                            <span class="badge badge-mute">Hoy sin consulta</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($sustitucionActiva)
            <div class="alert alert-warn mb-4">
                <x-icon name="repeat" />
                <p class="flex-1">
                    @if ($sustitucionActiva->titular_doctor_id === $doctor->id)
                        Actualmente lo está sustituyendo <strong>{{ $sustitucionActiva->substituteDoctor?->name }}</strong>
                    @else
                        Actualmente usted sustituye a <strong>{{ $sustitucionActiva->titularDoctor?->name }}</strong>
                    @endif
                    hasta el {{ $sustitucionActiva->end_date?->format('d/m/Y') }}.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4">
            <x-stat label="Mis pacientes" :value="$patients->count()" icon="patient" tone="info" />
            <x-stat label="Franjas de consulta" :value="$schedules->count()" icon="calendar" tone="brand" />
            <x-stat label="Horas semanales" :value="$horasSemana" icon="clock" tone="brand" hint="Tiempo asignado de consulta" />
            <x-stat label="Sustituciones" :value="$substitutions->count()" icon="repeat" tone="warn" hint="Historial completo" />
        </div>

        {{-- Agenda semanal --}}
        <div class="card mb-4">
            <div class="card-head">
                <h3 class="card-title">Mi horario de consulta</h3>
                <span class="badge badge-brand">{{ $horasSemana }} h semanales</span>
            </div>
            <div class="card-body">
                @if ($schedules->count())
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                        @foreach (\App\Models\Schedule::DAYS as $dia)
                            @php $franjas = $schedules->where('day_of_week', $dia); $esHoy = $dia === $hoy; @endphp
                            <div class="rounded-xl border p-2 {{ $esHoy ? 'border-teal-300 bg-teal-50/50' : 'border-slate-100' }}">
                                <p class="section-label mb-1.5">{{ mb_substr($dia, 0, 3) }} @if ($esHoy)<span class="text-teal-600">· hoy</span>@endif</p>
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
                    <x-empty icon="calendar" title="Sin horario asignado" />
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="card lg:col-span-2">
                <div class="card-head"><h3 class="card-title">Historial de sustituciones</h3></div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Rol</th><th>Con</th><th>Periodo</th><th>Estado</th></tr></thead>
                        <tbody>
                        @forelse ($substitutions as $substitution)
                            @php
                                $esTitular = $substitution->titular_doctor_id === $doctor->id;
                                $tono = ['Programada' => 'badge-info', 'Activa' => 'badge-ok', 'Finalizada' => 'badge-mute'][$substitution->status] ?? 'badge-mute';
                            @endphp
                            <tr>
                                <td><span class="badge {{ $esTitular ? 'badge-brand' : 'badge-warn' }}">{{ $esTitular ? 'Titular' : 'Sustituto' }}</span></td>
                                <td class="text-slate-700">{{ $esTitular ? $substitution->substituteDoctor?->name : $substitution->titularDoctor?->name }}</td>
                                <td class="num text-slate-500">{{ $substitution->start_date?->format('d/m/Y') }} – {{ $substitution->end_date?->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $tono }}">{{ $substitution->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><x-empty icon="repeat" title="Sin sustituciones" /></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-head"><h3 class="card-title">Mis vacaciones</h3></div>
                <div class="card-body">
                    <ul class="divide-y divide-slate-100 text-sm">
                        @forelse ($vacations as $vacation)
                            @php $tono = ['Planificadas' => 'badge-info', 'Disfrutadas' => 'badge-mute', 'Canceladas' => 'badge-bad'][$vacation->status] ?? 'badge-mute'; @endphp
                            <li class="py-2 flex items-center justify-between gap-2">
                                <div>
                                    <p class="num text-slate-700">{{ $vacation->start_date?->format('d/m/Y') }} – {{ $vacation->end_date?->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $vacation->reason ?? '—' }}</p>
                                </div>
                                <span class="badge {{ $tono }}">{{ $vacation->status }}</span>
                            </li>
                        @empty
                            <li><x-empty icon="palm" title="Sin vacaciones registradas" /></li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card lg:col-span-3">
                <div class="card-head">
                    <h3 class="card-title">Mis pacientes</h3>
                    <span class="badge badge-mute">{{ $patients->count() }}</span>
                </div>
                <div class="card-body">
                    @if ($patients->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($patients as $patient)
                                <div class="flex items-center gap-2 p-2 rounded-lg border border-slate-100">
                                    <span class="avatar avatar-sm" style="background: linear-gradient(135deg,#0d9488,#1d4ed8)">
                                        {{ mb_strtoupper(mb_substr($patient->name, 0, 1)) }}
                                    </span>
                                    <span class="text-sm text-slate-700 flex-1 truncate">{{ $patient->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $patient->phone ?? '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty icon="patient" title="Sin pacientes asignados" />
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
