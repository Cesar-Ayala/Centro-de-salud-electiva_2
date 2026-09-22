<?php

namespace Database\Factories;

use App\Models\DoctorVacation;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorVacationFactory extends Factory
{
    protected $model = DoctorVacation::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate   = (clone $startDate)->modify('+15 days');

        return [
            'doctor_id'  => Doctor::inRandomOrder()->first()?->id ?? Doctor::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
        ];
    }
}