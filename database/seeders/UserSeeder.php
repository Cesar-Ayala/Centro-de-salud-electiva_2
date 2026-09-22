<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Exception;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $password = Hash::make('password123');

            // 1. Obtener el primer médico y primer paciente creados por DoctorSeeder / PatientSeeder
            $doctor = Doctor::first();
            $patient = Patient::first();

            // 2. Crear los usuarios asignando las llaves foráneas si existen
            $testUsers = [
                [
                    'name'     => 'Administrador',
                    'email'    => 'admin@hospital.com',
                    'role'     => 'admin',
                    'password' => $password,
                ],
                [
                    'name'     => 'Operador',
                    'email'    => 'operador@hospital.com',
                    'role'     => 'operator',
                    'password' => $password,
                ],
                [
                    'name'      => 'Juan Pérez',
                    'email'     => 'juan.perez@hospital.com',
                    'role'      => 'doctor',
                    'doctor_id' => $doctor?->id, // Asocia el primer médico disponible
                    'password'  => $password,
                ],
                [
                    'name'       => 'Roberto Díaz',
                    'email'      => 'roberto.diaz@hospital.com',
                    'role'       => 'patient',
                    'patient_id' => $patient?->id, // Asocia el primer paciente disponible
                    'password'   => $password,
                ],
            ];

            foreach ($testUsers as $userData) {
                User::firstOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
            }

            $this->command->info('UserSeeder ejecutado con éxito y usuarios vinculados.');
        } catch (QueryException $e) {
            $this->command->error('Error de BD en UserSeeder: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->command->error('Error inesperado en UserSeeder: ' . $e->getMessage());
        }
    }
}