<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    /** Columnas por las que se puede ordenar el listado. */
    private const SORTABLE = ['name', 'nif', 'phone'];

    /** Listado de pacientes con su medico asignado. */
    public function index(Request $request): View
    {
        $sort = in_array($request->sort, self::SORTABLE, true) ? $request->sort : 'name';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100], true) ? (int) $request->per_page : 10;

        $patients = Patient::query()
            ->with('doctor')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%");
                });
            })
            ->when($request->doctor_id, fn ($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->when($request->unassigned, fn ($query) => $query->whereNull('doctor_id'))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('patients.index', [
            'patients' => $patients,
            'doctors' => Doctor::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('patients.create', [
            'patient' => new Patient(),
            'doctors' => Doctor::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Patient::create($this->validated($request));

        return redirect()->route('patients.index')->with('status', 'Paciente registrado correctamente.');
    }

    public function show(Patient $patient): View
    {
        $patient->load('doctor.schedules');

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', [
            'patient' => $patient,
            'doctors' => Doctor::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $patient->update($this->validated($request, $patient));

        return redirect()->route('patients.index')->with('status', 'Paciente actualizado correctamente.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('status', 'Paciente eliminado del sistema.');
    }

    private function validated(Request $request, ?Patient $patient = null): array
    {
        $id = $patient?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:15'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'nif' => ['required', 'string', 'max:20', 'unique:patients,nif'.($id ? ",{$id}" : '')],
            'social_security_number' => ['required', 'string', 'max:25', 'unique:patients,social_security_number'.($id ? ",{$id}" : '')],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
        ]);
    }
}
