<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorVacation;
use App\Models\Substitution;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class SubstitutionSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $vacations = DoctorVacation::all();
            $doctors = Doctor::all();

            if ($vacations->isEmpty() || $doctors->count() < 2) {
                $this->command->warn('SubstitutionSeeder omitido: Datos insuficientes para crear sustituciones.');
                return;
            }

            foreach ($vacations as $vacation) {
                $substitute = $doctors->where('id', '!=', $vacation->doctor_id)->random();

                Substitution::factory()->create([
                    'titular_doctor_id'    => $vacation->doctor_id,
                    'substitute_doctor_id' => $substitute->id,
                    'start_date'           => $vacation->start_date,
                    'end_date'             => $vacation->end_date,
                    'status'               => 'Programada',
                    'reason'               => 'Sustitución por periodo de vacaciones',
                ]);
            }

            $this->command->info('SubstitutionSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en SubstitutionSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en SubstitutionSeeder: ' . $e->getMessage());
        }
    }
}