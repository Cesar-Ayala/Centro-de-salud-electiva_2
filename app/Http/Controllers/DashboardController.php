<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorVacation;
use App\Models\Employee;
use App\Models\EmployeeVacation;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\Substitution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Panel principal: el contenido cambia segun el rol del usuario. */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->hasRole('doctor')) {
            return view('dashboard-doctor', $this->doctorData($user));
        }

        if ($user->hasRole('patient')) {
            return view('dashboard-patient', $this->patientData($user));
        }

        return view('dashboard', [
            'totalDoctors' => Doctor::count(),
            'activeDoctors' => Doctor::whereNull('discharge_date')->count(),
            'totalEmployees' => Employee::count(),
            'totalPatients' => Patient::count(),
            'totalSchedules' => Schedule::count(),
            'activeSubstitutions' => Substitution::where('status', 'Activa')->count(),
            'plannedDoctorVacations' => DoctorVacation::where('status', 'Planificadas')->count(),
            'plannedEmployeeVacations' => EmployeeVacation::where('status', 'Planificadas')->count(),
            'doctorsByType' => Doctor::selectRaw('doctor_type, COUNT(*) as total')
                ->groupBy('doctor_type')
                ->pluck('total', 'doctor_type'),
            'latestSubstitutions' => Substitution::with(['titularDoctor', 'substituteDoctor'])
                ->latest()
                ->take(5)
                ->get(),
            'upcomingVacations' => DoctorVacation::with('doctor')
                ->where('status', 'Planificadas')
                ->orderBy('start_date')
                ->take(5)
                ->get(),
        ] + $this->operationalData());
    }

    /**
     * Indicadores operativos del dia: cobertura, ausencias y avisos.
     * Se suman a las cifras generales del panel de administracion.
     */
    private function operationalData(): array
    {
        $today = now()->toDateString();

        // Medicos ausentes hoy por vacaciones.
        $absentToday = DoctorVacation::with('doctor')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('status', '!=', 'Canceladas')
            ->get();

        // Titulares que ya cuentan con un sustituto activo.
        $coveredIds = Substitution::where('status', 'Activa')
            ->pluck('titular_doctor_id')
            ->all();

        // Aviso principal: ausente hoy y sin nadie que lo reemplace.
        $uncovered = $absentToday->filter(fn ($vacation) => ! in_array($vacation->doctor_id, $coveredIds, true));

        $totalDoctors = Doctor::count();
        $doctorsWithSchedule = Schedule::distinct()->count('doctor_id');

        // Horas de consulta programadas por dia de la semana.
        $weeklyLoad = collect(Schedule::DAYS)->mapWithKeys(function ($day) {
            $minutes = Schedule::where('day_of_week', $day)->get()->sum(function ($schedule) {
                $start = strtotime((string) $schedule->start_time);
                $end = strtotime((string) $schedule->end_time);

                return $end > $start ? ($end - $start) / 60 : 0;
            });

            return [$day => round($minutes / 60, 1)];
        });

        return [
            'absentToday' => $absentToday,
            'uncoveredToday' => $uncovered,
            'coveredCount' => $absentToday->count() - $uncovered->count(),
            'doctorsWithSchedule' => $doctorsWithSchedule,
            'doctorsWithoutSchedule' => max(0, $totalDoctors - $doctorsWithSchedule),
            'patientsWithoutDoctor' => Patient::whereNull('doctor_id')->count(),
            'weeklyLoad' => $weeklyLoad,
            'topDoctors' => Doctor::withCount('patients')
                ->orderByDesc('patients_count')
                ->take(5)
                ->get(),
            'employeesByType' => Employee::selectRaw('employee_type, COUNT(*) as total')
                ->groupBy('employee_type')
                ->pluck('total', 'employee_type'),
        ];
    }

    /** Datos que ve un medico: solo su propia informacion. */
    private function doctorData($user): array
    {
        $doctor = $user->doctor;

        if (! $doctor) {
            return ['doctor' => null, 'schedules' => collect(), 'substitutions' => collect(), 'vacations' => collect(), 'patients' => collect()];
        }

        $schedules = $doctor->schedules()->get()
            ->sortBy(fn ($s) => array_search($s->day_of_week, Schedule::DAYS, true))
            ->values();

        return [
            'doctor' => $doctor,
            'schedules' => $schedules,
            'substitutions' => Substitution::with(['titularDoctor', 'substituteDoctor'])
                ->where('titular_doctor_id', $doctor->id)
                ->orWhere('substitute_doctor_id', $doctor->id)
                ->orderByDesc('start_date')
                ->get(),
            'vacations' => $doctor->vacations()->orderByDesc('start_date')->get(),
            'patients' => $doctor->patients()->orderBy('name')->get(),
        ];
    }

    /** Datos que ve un paciente: su medico asignado y el horario de atencion. */
    private function patientData($user): array
    {
        $patient = $user->patient;
        $doctor = $patient?->doctor;

        $schedules = $doctor
            ? $doctor->schedules()->get()
                ->sortBy(fn ($s) => array_search($s->day_of_week, Schedule::DAYS, true))
                ->values()
            : collect();

        $substitution = $doctor
            ? Substitution::with('substituteDoctor')
                ->where('titular_doctor_id', $doctor->id)
                ->where('status', 'Activa')
                ->first()
            : null;

        return [
            'patient' => $patient,
            'doctor' => $doctor,
            'schedules' => $schedules,
            'substitution' => $substitution,
        ];
    }
}
