<?php

namespace Database\Factories;

use App\Models\Substitution;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubstitutionFactory extends Factory
{
    protected $model = Substitution::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate   = (clone $startDate)->modify('+15 days');

        return [
            'titular_doctor_id'    => Doctor::inRandomOrder()->first()?->id ?? Doctor::factory(),
            'substitute_doctor_id' => Doctor::inRandomOrder()->first()?->id ?? Doctor::factory(),
            'start_date'           => $startDate->format('Y-m-d'),
            'end_date'             => $endDate->format('Y-m-d'),
            'status'               => $this->faker->randomElement(['Programada', 'Activa', 'Finalizada']),
            'reason'               => $this->faker->sentence(6),
        ];
    }
}