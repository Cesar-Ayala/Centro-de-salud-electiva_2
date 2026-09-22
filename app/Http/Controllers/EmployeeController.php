<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /** Columnas por las que se puede ordenar el listado. */
    private const SORTABLE = ['name', 'employee_type', 'nif', 'vacations_count'];

    /** Listado de personal no medico (ATS, auxiliares, celadores, administrativos). */
    public function index(Request $request): View
    {
        $sort = in_array($request->sort, self::SORTABLE, true) ? $request->sort : 'name';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100], true) ? (int) $request->per_page : 10;

        $employees = Employee::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%");
                });
            })
            ->when($request->employee_type, fn ($query, $type) => $query->where('employee_type', $type))
            ->withCount('vacations')
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create', ['employee' => new Employee()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Employee::create($this->validated($request));

        return redirect()->route('employees.index')->with('status', 'Empleado registrado correctamente.');
    }

    public function show(Employee $employee): View
    {
        $employee->load('vacations');

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($this->validated($request, $employee));

        return redirect()->route('employees.index')->with('status', 'Empleado actualizado correctamente.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('status', 'Empleado eliminado del sistema.');
    }

    private function validated(Request $request, ?Employee $employee = null): array
    {
        $id = $employee?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:15'],
            'town' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'nif' => ['required', 'string', 'max:20', 'unique:employees,nif'.($id ? ",{$id}" : '')],
            'social_security_number' => ['required', 'string', 'max:25', 'unique:employees,social_security_number'.($id ? ",{$id}" : '')],
            'employee_type' => ['required', 'in:ATS,ATS_Zona,Auxiliar,Celador,Administrativo'],
        ]);
    }
}
