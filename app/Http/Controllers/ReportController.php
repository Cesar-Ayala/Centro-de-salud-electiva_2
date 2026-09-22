<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorVacation;
use App\Models\EmployeeVacation;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\Substitution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /** Pantalla de reportes: reune las consultas del documento del proyecto. */
    public function index(Request $request): View
    {
        $doctorId = $request->integer('doctor_id') ?: null;

        $schedule = collect();
        $patients = collect();

        if ($doctorId) {
            $schedule = Schedule::where('doctor_id', $doctorId)
                ->get()
                ->sortBy(fn ($s) => array_search($s->day_of_week, Schedule::DAYS, true))
                ->values();

            $patients = Patient::where('doctor_id', $doctorId)->orderBy('name')->get();
        }

        return view('reports.index', [
            'doctors' => Doctor::orderBy('name')->get(),
            'doctorId' => $doctorId,
            'selectedDoctor' => $doctorId ? Doctor::find($doctorId) : null,
            'weeklyHours' => round($schedule->sum(function ($row) {
                $start = strtotime((string) $row->start_time);
                $end = strtotime((string) $row->end_time);

                return $end > $start ? ($end - $start) / 3600 : 0;
            }), 1),
            'schedule' => $schedule,
            'patients' => $patients,
            'activeSubstitutions' => Substitution::with(['titularDoctor', 'substituteDoctor'])
                ->where('status', 'Activa')
                ->orderBy('start_date')
                ->get(),
            'plannedDoctorVacations' => DoctorVacation::with('doctor')
                ->where('status', 'Planificadas')
                ->orderBy('start_date')
                ->get(),
            'plannedEmployeeVacations' => EmployeeVacation::with('employee')
                ->where('status', 'Planificadas')
                ->orderBy('start_date')
                ->get(),
        ]);
    }

    /** Consulta 1 (JSON): medicos en sustitucion activa. */
    public function activeSubstitutions(): JsonResponse
    {
        return response()->json(
            Substitution::with(['titularDoctor', 'substituteDoctor'])
                ->where('status', 'Activa')
                ->get()
        );
    }

    /** Consulta 2 (JSON): pacientes asignados a un medico. */
    public function patientsByDoctor(int $doctorId): JsonResponse
    {
        return response()->json(Patient::where('doctor_id', $doctorId)->get());
    }

    /** Consulta 3 (JSON): horario semanal de un medico. */
    public function doctorSchedule(int $doctorId): JsonResponse
    {
        $schedule = Schedule::where('doctor_id', $doctorId)
            ->get()
            ->sortBy(fn ($s) => array_search($s->day_of_week, Schedule::DAYS, true))
            ->values();

        return response()->json($schedule);
    }

    /** Consulta 4 (JSON): vacaciones planificadas de empleados. */
    public function plannedEmployeeVacations(): JsonResponse
    {
        return response()->json(
            EmployeeVacation::with('employee')
                ->where('status', 'Planificadas')
                ->get()
        );
    }
}
