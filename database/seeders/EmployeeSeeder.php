<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        try {
            Employee::factory()->count(15)->create();
            $this->command->info('EmployeeSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en EmployeeSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en EmployeeSeeder: ' . $e->getMessage());
        }
    }
}