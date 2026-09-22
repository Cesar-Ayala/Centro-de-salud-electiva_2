<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'doctor_id'   => Doctor::inRandomOrder()->first()?->id ?? Doctor::factory(),
            'day_of_week' => $this->faker->randomElement(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']),
            'start_time'  => '08:00:00',
            'end_time'    => '14:00:00',
        ];
    }
}