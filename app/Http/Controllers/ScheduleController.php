<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /** Horarios de consulta ordenados por medico y dia de la semana. */
    public function index(Request $request): View
    {
        $schedules = Schedule::with('doctor')
            ->when($request->doctor_id, fn ($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->when($request->day_of_week, fn ($query, $day) => $query->where('day_of_week', $day))
            ->get()
            ->sortBy([
                fn ($a, $b) => strcmp($a->doctor?->name ?? '', $b->doctor?->name ?? ''),
                fn ($a, $b) => array_search($a->day_of_week, Schedule::DAYS, true) <=> array_search($b->day_of_week, Schedule::DAYS, true),
            ])
            ->values();

        // Cuadro semanal: franjas agrupadas por medico y dia para la vista de calendario.
        $grid = $schedules->groupBy(fn ($schedule) => $schedule->doctor?->name ?? 'Sin médico')
            ->map(fn ($rows) => $rows->groupBy('day_of_week'));

        // Horas totales de consulta por dia de la semana (para la barra de carga).
        $loadPerDay = collect(Schedule::DAYS)->mapWithKeys(function ($day) use ($schedules) {
            $minutes = $schedules->where('day_of_week', $day)->sum(function ($schedule) {
                $start = strtotime((string) $schedule->start_time);
                $end = strtotime((string) $schedule->end_time);

                return $end > $start ? ($end - $start) / 60 : 0;
            });

            return [$day => round($minutes / 60, 1)];
        });

        return view('schedules.index', [
            'schedules' => $schedules,
            'doctors' => Doctor::orderBy('name')->get(),
            'days' => Schedule::DAYS,
            'grid' => $grid,
            'loadPerDay' => $loadPerDay,
        ]);
    }

    public function create(): View
    {
        return view('schedules.create', [
            'schedule' => new Schedule(),
            'doctors' => Doctor::orderBy('name')->get(),
            'days' => Schedule::DAYS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->ensureNoOverlap($data);

        Schedule::create($data);

        return redirect()->route('schedules.index')->with('status', 'Horario asignado correctamente.');
    }

    public function edit(Schedule $schedule): View
    {
        return view('schedules.edit', [
            'schedule' => $schedule,
            'doctors' => Doctor::orderBy('name')->get(),
            'days' => Schedule::DAYS,
        ]);
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $data = $this->validated($request);
        $this->ensureNoOverlap($data, $schedule->id);

        $schedule->update($data);

        return redirect()->route('schedules.index')->with('status', 'Horario actualizado correctamente.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('status', 'Horario eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'day_of_week' => ['required', 'in:'.implode(',', Schedule::DAYS)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
    }

    /** Evita que un mismo medico tenga dos franjas cruzadas el mismo dia. */
    private function ensureNoOverlap(array $data, ?int $ignoreId = null): void
    {
        $exists = Schedule::where('doctor_id', $data['doctor_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_time' => 'El médico ya tiene una franja de consulta que se cruza con ese horario.',
            ]);
        }
    }
}
