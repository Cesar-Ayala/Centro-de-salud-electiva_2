@extends('layouts.app')
@section('title', 'Mi atención médica')
@section('subtitle', 'Consulta de solo lectura')
@section('breadcrumb', 'Mi atención')

@section('content')
    @if (! $patient)
        <div class="card">
            <x-empty icon="patient" title="Su cuenta no está vinculada a una ficha de paciente"
                     hint="Comuníquese con la administración del centro de salud." />
        </div>
    @else
        @php
            $hoy = \App\Models\Schedule::DAYS[(int) now()->format('N') - 1] ?? null;
            $consultaHoy = $schedules->where('day_of_week', $hoy);
        @endphp

        <div class="card mb-4 overflow-hidden fade-in">
            <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-4"
                 style="background: linear-gradient(120deg, #0a2540 0%, #12495a 60%, #0d9488 100%);">
                <span class="avatar avatar-lg" style="background: rgba(255,255,255,.14)">
                    {{ mb_strtoupper(mb_substr($patient->name, 0, 2)) }}
                </span>
                <div class="flex-1 text-white">
                    <h2 class="text-xl font-semibold">{{ $patient->name }}</h2>
                    <p class="text-sm text-slate-300 mt-1">
                        @if ($doctor)
                            Médico asignado: {{ $doctor->name }}
                        @else
                            Todavía no tiene médico asignado
                        @endif
                    </p>
                    @if ($consultaHoy->count())
                        <span class="badge badge-ok mt-2"><span class="dot"></span>
                            Hoy hay consulta: {{ substr($consultaHoy->first()->start_time, 0, 5) }} – {{ substr($consultaHoy->first()->end_time, 0, 5) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if ($substitution)
            <div class="alert alert-warn mb-4">
                <x-icon name="repeat" />
                <p class="flex-1">
                    Su médico está siendo sustituido por <strong>{{ $substitution->substituteDoctor?->name }}</strong>
                    hasta el {{ $substitution->end_date?->format('d/m/Y') }}. La atención continúa en el mismo horario.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="card">
                <div class="card-head"><h3 class="card-title">Mis datos</h3></div>
                <div class="card-body space-y-3 text-sm">
                    @php
                        $datos = [
                            ['id', 'NIF / Cédula', $patient->nif],
                            ['phone', 'Teléfono', $patient->phone ?? '—'],
                            ['pin', 'Dirección', $patient->address ?? '—'],
                        ];
                    @endphp
                    @foreach ($datos as [$icono, $etiqueta, $valor])
                        <div class="flex items-start gap-3">
                            <span class="icon-chip chip-ink" style="width:32px;height:32px;border-radius:9px"><x-icon :name="$icono" class="w-4 h-4" /></span>
                            <div><p class="section-label">{{ $etiqueta }}</p><p class="text-slate-800">{{ $valor }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-head"><h3 class="card-title">Médico asignado</h3></div>
                <div class="card-body">
                    @if ($doctor)
                        <div class="flex items-center gap-3">
                            <span class="avatar">{{ mb_strtoupper(mb_substr($doctor->name, 0, 2)) }}</span>
                            <div>
                                <p class="font-medium text-slate-900">{{ $doctor->name }}</p>
                                <p class="text-xs text-slate-500">{{ $doctor->doctor_type }} · Colegiado {{ $doctor->collegiate_number }}</p>
                            </div>
                        </div>
                        @if ($doctor->phone)
                            <p class="text-sm text-slate-500 mt-3 flex items-center gap-2">
                                <x-icon name="phone" class="w-4 h-4" />{{ $doctor->phone }}
                            </p>
                        @endif
                    @else
                        <x-empty icon="stethoscope" title="Aún no tiene médico asignado" />
                    @endif
                </div>
            </div>

            <div class="card lg:col-span-2">
                <div class="card-head"><h3 class="card-title">Horario de atención</h3></div>
                <div class="card-body">
                    @if ($schedules->count())
                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                            @foreach (\App\Models\Schedule::DAYS as $dia)
                                @php $franjas = $schedules->where('day_of_week', $dia); $esHoy = $dia === $hoy; @endphp
                                <div class="rounded-xl border p-2 {{ $esHoy ? 'border-teal-300 bg-teal-50/50' : 'border-slate-100' }}">
                                    <p class="section-label mb-1.5">{{ mb_substr($dia, 0, 3) }}</p>
                                    @forelse ($franjas as $franja)
                                        <div class="slot mb-1">
                                            <strong>{{ substr($franja->start_time, 0, 5) }}</strong>
                                            {{ substr($franja->end_time, 0, 5) }}
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-300">—</p>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty icon="calendar" title="Sin horario disponible" />
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
