<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        try {
            Patient::factory()->count(30)->create();
            $this->command->info('PatientSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en PatientSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en PatientSeeder: ' . $e->getMessage());
        }
    }
}