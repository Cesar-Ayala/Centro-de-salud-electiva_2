<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Exception;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        try {
            Doctor::factory()->count(15)->create();
            $this->command->info('DoctorSeeder ejecutado con éxito.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en DoctorSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en DoctorSeeder: ' . $e->getMessage());
        }
    }
}