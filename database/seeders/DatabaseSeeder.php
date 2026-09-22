<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->call([
            DoctorSeeder::class,
            EmployeeSeeder::class,
            PatientSeeder::class,
            ScheduleSeeder::class,
            DoctorVacationSeeder::class,
            EmployeeVacationSeeder::class,
            SubstitutionSeeder::class,
            UserSeeder::class, 
]);
            DB::commit();
            $this->command->info('¡Todos los seeders se ejecutaron y guardaron exitosamente!');
        } catch (Throwable $e) {
            DB::rollBack();
            $this->command->error('Error general durante la ejecución de los seeders: ' . $e->getMessage());
        }
    }
}