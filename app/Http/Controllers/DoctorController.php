<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorVacation;
use App\Models\Schedule;
use App\Models\Substitution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /** Columnas por las que se puede ordenar el listado. */
    private const SORTABLE = ['name', 'doctor_type', 'nif', 'collegiate_number', 'patients_count', 'hiring_date'];

    /** Listado de medicos con buscador, ordenamiento y paginacion configurable. */
    public function index(Request $request): View
    {
        $sort = in_array($request->sort, self::SORTABLE, true) ? $request->sort : 'name';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100], true) ? (int) $request->per_page : 10;

        $doctors = Doctor::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%")
                      ->orWhere('collegiate_number', 'like', "%{$search}%");
                });
            })
            ->when($request->doctor_type, fn ($query, $type) => $query->where('doctor_type', $type))
            ->when($request->status === 'activos', fn ($query) => $query->whereNull('discharge_date'))
            ->when($request->status === 'baja', fn ($query) => $query->whereNotNull('discharge_date'))
            ->withCount('patients')
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $today = now()->toDateString();

        // Estado real de cada medico el dia de hoy: de vacaciones y/o sustituido.
        $onVacation = DoctorVacation::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('status', '!=', 'Canceladas')
            ->pluck('doctor_id')
            ->all();

        $substituted = Substitution::where('status', 'Activa')
            ->pluck('titular_doctor_id')
            ->all();

        return view('doctors.index', compact('doctors', 'onVacation', 'substituted'));
    }

    public function create(): View
    {
        return view('doctors.create', ['doctor' => new Doctor()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Doctor::create($this->validated($request));

        return redirect()->route('doctors.index')->with('status', 'Médico registrado correctamente.');
    }

    /** Ficha del medico con sus horarios, pacientes, vacaciones y sustituciones. */
    public function show(Doctor $doctor): View
    {
        $doctor->load(['patients', 'vacations', 'substitutionsAsTitular.substituteDoctor', 'substitutionsAsSubstitute.titularDoctor']);

        $schedules = $doctor->schedules()->get()
            ->sortBy(fn ($s) => array_search($s->day_of_week, Schedule::DAYS, true))
            ->values();

        return view('doctors.show', compact('doctor', 'schedules'));
    }

    public function edit(Doctor $doctor): View
    {
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update($this->validated($request, $doctor));

        return redirect()->route('doctors.index')->with('status', 'Médico actualizado correctamente.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->delete();

        return redirect()->route('doctors.index')->with('status', 'Médico eliminado del sistema.');
    }

    /** Reglas de validacion compartidas por store() y update(). */
    private function validated(Request $request, ?Doctor $doctor = null): array
    {
        $id = $doctor?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:15'],
            'town' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'nif' => ['required', 'string', 'max:20', 'unique:doctors,nif'.($id ? ",{$id}" : '')],
            'social_security_number' => ['required', 'string', 'max:25', 'unique:doctors,social_security_number'.($id ? ",{$id}" : '')],
            'collegiate_number' => ['required', 'string', 'max:20', 'unique:doctors,collegiate_number'.($id ? ",{$id}" : '')],
            'doctor_type' => ['required', 'in:Titular,Interino,Sustituto'],
            'hiring_date' => ['nullable', 'date'],
            'discharge_date' => ['nullable', 'date', 'after_or_equal:hiring_date'],
        ]);
    }
}
