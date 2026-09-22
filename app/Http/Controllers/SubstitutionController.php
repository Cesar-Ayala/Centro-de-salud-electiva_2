<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Substitution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubstitutionController extends Controller
{
    /** Control de reemplazos temporales entre medicos. */
    public function index(Request $request): View
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
            ->paginate(in_array((int) $request->per_page, [10, 25, 50, 100], true) ? (int) $request->per_page : 10)
            ->withQueryString();

        return view('substitutions.index', [
            'substitutions' => $substitutions,
            'doctors' => Doctor::orderBy('name')->get(),
            'statuses' => Substitution::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('substitutions.create', [
            'substitution' => new Substitution(),
            'doctors' => Doctor::orderBy('name')->get(),
            'statuses' => Substitution::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Substitution::create($this->validated($request));

        return redirect()->route('substitutions.index')->with('status', 'Sustitución registrada correctamente.');
    }

    public function edit(Substitution $substitution): View
    {
        return view('substitutions.edit', [
            'substitution' => $substitution,
            'doctors' => Doctor::orderBy('name')->get(),
            'statuses' => Substitution::STATUSES,
        ]);
    }

    public function update(Request $request, Substitution $substitution): RedirectResponse
    {
        $substitution->update($this->validated($request));

        return redirect()->route('substitutions.index')->with('status', 'Sustitución actualizada correctamente.');
    }

    public function destroy(Substitution $substitution): RedirectResponse
    {
        $substitution->delete();

        return redirect()->route('substitutions.index')->with('status', 'Sustitución eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'titular_doctor_id' => ['required', 'exists:doctors,id'],
            'substitute_doctor_id' => ['required', 'exists:doctors,id', 'different:titular_doctor_id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:'.implode(',', Substitution::STATUSES)],
            'reason' => ['nullable', 'string', 'max:200'],
        ], [
            'substitute_doctor_id.different' => 'El médico sustituto debe ser distinto del titular.',
        ]);
    }
}
