<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeVacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeVacationController extends Controller
{
    /** Planificacion y seguimiento de vacaciones del personal no medico. */
    public function index(Request $request): View
    {
        $vacations = EmployeeVacation::with('employee')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->employee_id, fn ($query, $employeeId) => $query->where('employee_id', $employeeId))
            ->orderByDesc('start_date')
            ->paginate(in_array((int) $request->per_page, [10, 25, 50, 100], true) ? (int) $request->per_page : 10)
            ->withQueryString();

        return view('employee-vacations.index', [
            'vacations' => $vacations,
            'employees' => Employee::orderBy('name')->get(),
            'statuses' => EmployeeVacation::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('employee-vacations.create', [
            'vacation' => new EmployeeVacation(),
            'employees' => Employee::orderBy('name')->get(),
            'statuses' => EmployeeVacation::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        EmployeeVacation::create($this->validated($request));

        return redirect()->route('employee-vacations.index')->with('status', 'Vacaciones registradas correctamente.');
    }

    public function edit(EmployeeVacation $employeeVacation): View
    {
        return view('employee-vacations.edit', [
            'vacation' => $employeeVacation,
            'employees' => Employee::orderBy('name')->get(),
            'statuses' => EmployeeVacation::STATUSES,
        ]);
    }

    public function update(Request $request, EmployeeVacation $employeeVacation): RedirectResponse
    {
        $employeeVacation->update($this->validated($request));

        return redirect()->route('employee-vacations.index')->with('status', 'Vacaciones actualizadas correctamente.');
    }

    public function destroy(EmployeeVacation $employeeVacation): RedirectResponse
    {
        $employeeVacation->delete();

        return redirect()->route('employee-vacations.index')->with('status', 'Registro de vacaciones eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:'.implode(',', EmployeeVacation::STATUSES)],
            'reason' => ['nullable', 'string', 'max:200'],
            'observations' => ['nullable', 'string'],
        ]);
    }
}
