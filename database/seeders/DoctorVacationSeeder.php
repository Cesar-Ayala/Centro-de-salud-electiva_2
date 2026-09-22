<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorVacation;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class DoctorVacationSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $doctors = Doctor::all();

            if ($doctors->isEmpty()) {
                $this->command->warn('DoctorVacationSeeder omitido: No existen médicos.');
                return;
            }

            foreach ($doctors->random(min(5, $doctors->count())) as $doctor) {
                DoctorVacation::factory()->create([
                    'doctor_id' => $doctor->id,
                ]);
            }

            $this->command->info('DoctorVacationSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en DoctorVacationSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en DoctorVacationSeeder: ' . $e->getMessage());
        }
    }
}