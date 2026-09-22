@extends('layouts.app')
@section('title', 'Ficha del paciente')
@section('subtitle', $patient->name)
@section('breadcrumb', 'Pacientes')

@section('actions')
    <a href="{{ route('patients.index') }}" class="btn btn-ghost"><x-icon name="back" />Volver</a>
    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary"><x-icon name="pencil" />Editar ficha</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>
@endsection

@section('content')
    <div class="card mb-4 fade-in">
        <div class="p-5 flex flex-col md:flex-row md:items-center gap-4">
            <span class="avatar avatar-lg" style="background: linear-gradient(135deg,#0d9488,#1d4ed8)">
                {{ mb_strtoupper(mb_substr($patient->name, 0, 2)) }}
            </span>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-slate-900">{{ $patient->name }}</h2>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <span class="badge badge-mute">NIF {{ $patient->nif }}</span>
                    @if ($patient->doctor)
                        <span class="badge badge-brand">Médico: {{ $patient->doctor->name }}</span>
                    @else
                        <span class="badge badge-warn">Sin médico asignado</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="card-head"><h3 class="card-title">Datos del paciente</h3></div>
            <div class="card-body space-y-3 text-sm">
                @php
                    $datos = [
                        ['id', 'NIF / Cédula', $patient->nif],
                        ['shield', 'Seguridad social', $patient->social_security_number],
                        ['phone', 'Teléfono', $patient->phone ?? '—'],
                        ['pin', 'Dirección', $patient->address ?? '—'],
                        ['mail', 'Código postal', $patient->postal_code ?? '—'],
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
            <div class="card-head">
                <h3 class="card-title">Horario de atención de su médico</h3>
                @if ($patient->doctor)
                    <a href="{{ route('doctors.show', $patient->doctor) }}" class="link text-xs no-print">Ver médico</a>
                @endif
            </div>
            <div class="card-body">
                @php $agenda = $patient->doctor?->schedules ?? collect(); @endphp
                @if ($agenda->count())
                    <div class="space-y-2">
                        @foreach (\App\Models\Schedule::DAYS as $dia)
                            @php $franjas = $agenda->where('day_of_week', $dia); @endphp
                            @if ($franjas->count())
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="badge badge-mute w-24 justify-center">{{ $dia }}</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($franjas as $franja)
                                            <span class="slot">{{ substr($franja->start_time, 0, 5) }} – {{ substr($franja->end_time, 0, 5) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <x-empty icon="calendar" title="Sin horario disponible"
                             hint="El médico asignado todavía no tiene franjas de consulta." />
                @endif
            </div>
        </div>
    </div>
@endsection
