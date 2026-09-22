<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Buscador global: consulta medicos, empleados y pacientes
 * a partir de un unico termino (nombre, NIF o numero de colegiado).
 */
class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->input('q', ''));

        $doctors = collect();
        $employees = collect();
        $patients = collect();

        if (mb_strlen($term) >= 2) {
            $like = "%{$term}%";

            $doctors = Doctor::withCount('patients')
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                          ->orWhere('nif', 'like', $like)
                          ->orWhere('collegiate_number', 'like', $like)
                          ->orWhere('phone', 'like', $like);
                })
                ->orderBy('name')
                ->limit(15)
                ->get();

            $employees = Employee::where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                          ->orWhere('nif', 'like', $like)
                          ->orWhere('phone', 'like', $like);
                })
                ->orderBy('name')
                ->limit(15)
                ->get();

            $patients = Patient::with('doctor')
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                          ->orWhere('nif', 'like', $like)
                          ->orWhere('phone', 'like', $like);
                })
                ->orderBy('name')
                ->limit(15)
                ->get();
        }

        return view('search.index', [
            'term' => $term,
            'doctors' => $doctors,
            'employees' => $employees,
            'patients' => $patients,
            'total' => $doctors->count() + $employees->count() + $patients->count(),
        ]);
    }
}
