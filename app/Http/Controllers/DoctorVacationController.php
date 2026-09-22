<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorVacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorVacationController extends Controller
{
    /** Planificacion y seguimiento de vacaciones de medicos. */
    public function index(Request $request): View
    {
        $vacations = DoctorVacation::with('doctor')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->doctor_id, fn ($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->orderByDesc('start_date')
            ->paginate(in_array((int) $request->per_page, [10, 25, 50, 100], true) ? (int) $request->per_page : 10)
            ->withQueryString();

        return view('doctor-vacations.index', [
            'vacations' => $vacations,
            'doctors' => Doctor::orderBy('name')->get(),
            'statuses' => DoctorVacation::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('doctor-vacations.create', [
            'vacation' => new DoctorVacation(),
            'doctors' => Doctor::orderBy('name')->get(),
            'statuses' => DoctorVacation::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        DoctorVacation::create($this->validated($request));

        return redirect()->route('doctor-vacations.index')->with('status', 'Vacaciones registradas correctamente.');
    }

    public function edit(DoctorVacation $doctorVacation): View
    {
        return view('doctor-vacations.edit', [
            'vacation' => $doctorVacation,
            'doctors' => Doctor::orderBy('name')->get(),
            'statuses' => DoctorVacation::STATUSES,
        ]);
    }

    public function update(Request $request, DoctorVacation $doctorVacation): RedirectResponse
    {
        $doctorVacation->update($this->validated($request));

        return redirect()->route('doctor-vacations.index')->with('status', 'Vacaciones actualizadas correctamente.');
    }

    public function destroy(DoctorVacation $doctorVacation): RedirectResponse
    {
        $doctorVacation->delete();

        return redirect()->route('doctor-vacations.index')->with('status', 'Registro de vacaciones eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:'.implode(',', DoctorVacation::STATUSES)],
            'reason' => ['nullable', 'string', 'max:200'],
            'observations' => ['nullable', 'string'],
        ]);
    }
}
