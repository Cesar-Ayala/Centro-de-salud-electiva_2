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
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Exportacion a CSV de cualquiera de los listados del sistema.
 * Respeta los mismos filtros que la pantalla desde la que se descarga,
 * de modo que lo exportado coincide con lo que el usuario esta viendo.
 */
class ExportController extends Controller
{
    /** Recursos habilitados para exportar. */
    private const RESOURCES = [
        'doctors',
        'employees',
        'patients',
        'schedules',
        'substitutions',
        'doctor-vacations',
        'employee-vacations',
    ];

    public function download(Request $request, string $resource): StreamedResponse
    {
        abort_unless(in_array($resource, self::RESOURCES, true), 404);

        [$headers, $rows] = match ($resource) {
            'doctors' => $this->doctors($request),
            'employees' => $this->employees($request),
            'patients' => $this->patients($request),
            'schedules' => $this->schedules($request),
            'substitutions' => $this->substitutions($request),
            'doctor-vacations' => $this->doctorVacations($request),
            'employee-vacations' => $this->employeeVacations($request),
        };

        return $this->stream($resource, $headers, $rows);
    }

    /** Genera el archivo CSV sin cargar todo en memoria. */
    private function stream(string $resource, array $headers, array $rows): StreamedResponse
    {
        $filename = $resource.'-'.now()->format('Y-m-d_H-i').'.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // BOM para que Excel reconozca los acentos.
            fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function date($value): string
    {
        return $value ? $value->format('d/m/Y') : '';
    }

    private function doctors(Request $request): array
    {
        $doctors = Doctor::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%")
                      ->orWhere('collegiate_number', 'like', "%{$search}%");
                });
            })
            ->when($request->doctor_type, fn ($query, $type) => $query->where('doctor_type', $type))
            ->withCount('patients')
            ->orderBy('name')
            ->get();

        $rows = $doctors->map(fn ($doctor) => [
            $doctor->name,
            $doctor->doctor_type,
            $doctor->nif,
            $doctor->social_security_number,
            $doctor->collegiate_number,
            $doctor->phone,
            $doctor->town,
            $doctor->province,
            $doctor->patients_count,
            $this->date($doctor->hiring_date),
            $this->date($doctor->discharge_date),
            $doctor->is_active ? 'Activo' : 'De baja',
        ])->all();

        return [
            ['Nombre', 'Tipo', 'NIF', 'Seguridad social', 'Colegiado', 'Teléfono', 'Población', 'Provincia', 'Pacientes', 'Alta', 'Baja', 'Estado'],
            $rows,
        ];
    }

    private function employees(Request $request): array
    {
        $employees = Employee::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%");
                });
            })
            ->when($request->employee_type, fn ($query, $type) => $query->where('employee_type', $type))
            ->withCount('vacations')
            ->orderBy('name')
            ->get();

        $rows = $employees->map(fn ($employee) => [
            $employee->name,
            str_replace('_', ' de ', $employee->employee_type),
            $employee->nif,
            $employee->social_security_number,
            $employee->phone,
            $employee->town,
            $employee->vacations_count,
        ])->all();

        return [
            ['Nombre', 'Cargo', 'NIF', 'Seguridad social', 'Teléfono', 'Población', 'Periodos de vacaciones'],
            $rows,
        ];
    }

    private function patients(Request $request): array
    {
        $patients = Patient::query()
            ->with('doctor')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%");
                });
            })
            ->when($request->doctor_id, fn ($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->orderBy('name')
            ->get();

        $rows = $patients->map(fn ($patient) => [
            $patient->name,
            $patient->nif,
            $patient->social_security_number,
            $patient->phone,
            $patient->address,
            $patient->doctor?->name ?? 'Sin asignar',
        ])->all();

        return [
            ['Nombre', 'NIF', 'Seguridad social', 'Teléfono', 'Dirección', 'Médico asignado'],
            $rows,
        ];
    }

    private function schedules(Request $request): array
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

        $rows = $schedules->map(fn ($schedule) => [
            $schedule->doctor?->name,
            $schedule->day_of_week,
            substr((string) $schedule->start_time, 0, 5),
            substr((string) $schedule->end_time, 0, 5),
        ])->all();

        return [['Médico', 'Día', 'Inicio', 'Fin'], $rows];
    }

    private function substitutions(Request $request): array
    {
        $substitutions = Substitution::with(['titularDoctor', 'substituteDoctor'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->doctor_id, function ($query, $doctorId) {
                $query->where(function ($q) use ($doctorId) {
                    $q->where('titular_doctor_id', $doctorId)
                      ->orWhere('substitute_doctor_id', $doctorId);
                });
            })
            ->orderByDesc('start_date')
            ->get();

        $rows = $substitutions->map(fn ($substitution) => [
            $substitution->titularDoctor?->name,
            $substitution->substituteDoctor?->name,
            $this->date($substitution->start_date),
            $this->date($substitution->end_date),
            $substitution->reason,
            $substitution->status,
        ])->all();

        return [['Médico titular', 'Médico sustituto', 'Desde', 'Hasta', 'Motivo', 'Estado'], $rows];
    }

    private function doctorVacations(Request $request): array
    {
        $vacations = DoctorVacation::with('doctor')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->doctor_id, fn ($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->orderByDesc('start_date')
            ->get();

        $rows = $vacations->map(fn ($vacation) => [
            $vacation->doctor?->name,
            $this->date($vacation->start_date),
            $this->date($vacation->end_date),
            $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : '',
            $vacation->reason,
            $vacation->status,
        ])->all();

        return [['Médico', 'Desde', 'Hasta', 'Días', 'Motivo', 'Estado'], $rows];
    }

    private function employeeVacations(Request $request): array
    {
        $vacations = EmployeeVacation::with('employee')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->employee_id, fn ($query, $employeeId) => $query->where('employee_id', $employeeId))
            ->orderByDesc('start_date')
            ->get();

        $rows = $vacations->map(fn ($vacation) => [
            $vacation->employee?->name,
            $this->date($vacation->start_date),
            $this->date($vacation->end_date),
            $vacation->start_date && $vacation->end_date ? $vacation->start_date->diffInDays($vacation->end_date) + 1 : '',
            $vacation->reason,
            $vacation->status,
        ])->all();

        return [['Empleado', 'Desde', 'Hasta', 'Días', 'Motivo', 'Estado'], $rows];
    }
}
