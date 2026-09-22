@extends('layouts.app')
@section('title', 'Horarios de consulta')
@section('subtitle', 'Días y franjas horarias asignadas a cada médico')
@section('breadcrumb', 'Horarios')

@section('actions')
    <a href="{{ route('schedules.create') }}" class="btn btn-primary"><x-icon name="plus" />Nuevo horario</a>
    <a href="{{ route('exports.download', ['resource' => 'schedules'] + request()->only(['doctor_id', 'day_of_week'])) }}"
       class="btn btn-ghost"><x-icon name="download" />Exportar CSV</a>
    <button type="button" onclick="window.print()" class="btn btn-ghost"><x-icon name="printer" />Imprimir</button>

    <div class="ml-auto inline-flex rounded-lg border border-slate-200 bg-white p-0.5">
        <button type="button" id="tabGrid" class="btn btn-sm btn-soft"><x-icon name="calendar" />Cuadro semanal</button>
        <button type="button" id="tabList" class="btn btn-sm btn-ghost" style="border-color:transparent"><x-icon name="dashboard" />Listado</button>
    </div>
@endsection

@section('content')
    {{-- Filtros --}}
    <div class="card mb-4 no-print">
        <div class="p-4">
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
                    <label class="field-label">Día</label>
                    <select name="day_of_week" class="select">
                        <option value="">Todos los días</option>
                        @foreach ($days as $day)
                            <option value="{{ $day }}" @selected(request('day_of_week') === $day)>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-dark"><x-icon name="filter" />Filtrar</button>
                <a href="{{ route('schedules.index') }}" class="btn btn-ghost">Limpiar</a>

                <div class="ml-auto flex items-center gap-4 text-xs text-slate-500">
                    <span><strong class="text-slate-900">{{ $schedules->count() }}</strong> franjas</span>
                    <span><strong class="text-slate-900">{{ $grid->count() }}</strong> médicos con agenda</span>
                    <span><strong class="text-slate-900">{{ $loadPerDay->sum() }} h</strong> semanales</span>
                </div>
            </form>
        </div>
    </div>

    {{-- Vista 1: cuadro semanal --}}
    <div id="viewGrid" class="space-y-4">
        <div class="card overflow-hidden">
            <div class="card-head">
                <h2 class="card-title">Cuadro semanal de consulta</h2>
                <span class="badge badge-brand">{{ now()->format('d/m/Y') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table" style="min-width: 900px">
                    <thead>
                        <tr>
                            <th style="width: 200px">Médico</th>
                            @foreach ($days as $day)
                                <th class="text-center">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($grid as $doctorName => $porDia)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($doctorName, 0, 1)) }}</span>
                                    <span class="font-medium text-slate-900">{{ $doctorName }}</span>
                                </div>
                            </td>
                            @foreach ($days as $day)
                                <td class="align-top">
                                    @forelse ($porDia->get($day, collect()) as $slot)
                                        <div class="slot mb-1">
                                            <strong>{{ substr($slot->start_time, 0, 5) }} – {{ substr($slot->end_time, 0, 5) }}</strong>
                                            <a href="{{ route('schedules.edit', $slot) }}" class="link text-[.68rem] no-print">Editar</a>
                                        </div>
                                    @empty
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($days) + 1 }}">
                            <x-empty icon="calendar" title="No hay horarios registrados"
                                     hint="Asigne franjas de consulta a los médicos para ver el cuadro semanal." />
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Carga por día --}}
        <div class="card">
            <div class="card-head"><h2 class="card-title">Carga de consulta por día</h2></div>
            <div class="card-body space-y-2">
                @php $maxDia = max(1, $loadPerDay->max()); @endphp
                @foreach ($loadPerDay as $day => $hours)
                    <div class="flex items-center gap-3 text-sm">
                        <span class="w-24 text-slate-500">{{ $day }}</span>
                        <div class="progress flex-1"><span style="width: {{ round($hours / $maxDia * 100) }}%"></span></div>
                        <span class="w-14 text-right num font-medium text-slate-700">{{ $hours }} h</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Vista 2: listado --}}
    <div id="viewList" class="hidden">
        <div class="card">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Médico</th><th>Día</th><th>Inicio</th><th>Fin</th><th>Duración</th><th class="text-right no-print">Acciones</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($schedules as $schedule)
                        @php
                            $inicio = strtotime((string) $schedule->start_time);
                            $fin = strtotime((string) $schedule->end_time);
                            $duracion = $fin > $inicio ? round(($fin - $inicio) / 3600, 1) : 0;
                        @endphp
                        <tr>
                            <td class="font-medium text-slate-900">{{ $schedule->doctor?->name }}</td>
                            <td><span class="badge badge-mute">{{ $schedule->day_of_week }}</span></td>
                            <td class="num">{{ substr($schedule->start_time, 0, 5) }}</td>
                            <td class="num">{{ substr($schedule->end_time, 0, 5) }}</td>
                            <td class="num text-slate-500">{{ $duracion }} h</td>
                            <td class="text-right whitespace-nowrap no-print">
                                <div class="inline-flex gap-1">
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-ghost" title="Editar"><x-icon name="pencil" /></a>
                                    @if (auth()->user()->isAdmin())
                                        <x-delete-form :action="route('schedules.destroy', $schedule)"
                                                       message="Se eliminará la franja de {{ $schedule->day_of_week }} del médico {{ $schedule->doctor?->name }}." />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty icon="calendar" title="No hay horarios registrados" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const grid = document.getElementById('viewGrid');
        const list = document.getElementById('viewList');
        const tabGrid = document.getElementById('tabGrid');
        const tabList = document.getElementById('tabList');

        function activar(tab) {
            const esGrid = tab === 'grid';
            grid.classList.toggle('hidden', !esGrid);
            list.classList.toggle('hidden', esGrid);
            tabGrid.className = 'btn btn-sm ' + (esGrid ? 'btn-soft' : 'btn-ghost');
            tabList.className = 'btn btn-sm ' + (esGrid ? 'btn-ghost' : 'btn-soft');
            if (!esGrid) { tabGrid.style.borderColor = 'transparent'; tabList.style.borderColor = ''; }
            else { tabList.style.borderColor = 'transparent'; tabGrid.style.borderColor = ''; }
            try { localStorage.setItem('schedulesView', tab); } catch (e) {}
        }

        tabGrid?.addEventListener('click', () => activar('grid'));
        tabList?.addEventListener('click', () => activar('list'));

        try {
            const guardada = localStorage.getItem('schedulesView');
            if (guardada === 'list') activar('list');
        } catch (e) {}
    })();
</script>
@endpush
