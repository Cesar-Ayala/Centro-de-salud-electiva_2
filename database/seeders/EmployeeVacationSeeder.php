<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeVacation;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class EmployeeVacationSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $employees = Employee::all();

            if ($employees->isEmpty()) {
                $this->command->warn('EmployeeVacationSeeder omitido: No existen empleados.');
                return;
            }

            foreach ($employees->random(min(5, $employees->count())) as $employee) {
                EmployeeVacation::factory()->create([
                    'employee_id' => $employee->id,
                ]);
            }

            $this->command->info('EmployeeVacationSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en EmployeeVacationSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en EmployeeVacationSeeder: ' . $e->getMessage());
        }
    }
}