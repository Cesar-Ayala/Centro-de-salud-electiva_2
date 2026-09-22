<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $doctors = Doctor::all();

            if ($doctors->isEmpty()) {
                $this->command->warn('ScheduleSeeder omitido: No existen médicos registrados.');
                return;
            }

            foreach ($doctors as $doctor) {
                Schedule::factory()->count(2)->create([
                    'doctor_id' => $doctor->id,
                ]);
            }

            $this->command->info('ScheduleSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en ScheduleSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en ScheduleSeeder: ' . $e->getMessage());
        }
    }
}