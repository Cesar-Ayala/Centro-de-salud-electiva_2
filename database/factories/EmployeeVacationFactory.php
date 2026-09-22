<?php

namespace Database\Factories;

use App\Models\EmployeeVacation;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeVacationFactory extends Factory
{
    protected $model = EmployeeVacation::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate   = (clone $startDate)->modify('+15 days');

        return [
            'employee_id' => Employee::inRandomOrder()->first()?->id ?? Employee::factory(),
            'start_date'  => $startDate->format('Y-m-d'),
            'end_date'    => $endDate->format('Y-m-d'),
        ];
    }
}