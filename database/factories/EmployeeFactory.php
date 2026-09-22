<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name'                   => $this->faker->name(),
            'address'                => $this->faker->streetAddress(),
            'phone'                  => $this->faker->numerify('##########'),
            'town'                   => $this->faker->city(),
            'province'               => $this->faker->state(),
            'postal_code'            => $this->faker->postcode(),
            'nif'                    => strtoupper($this->faker->bothify('########?')),
            'social_security_number' => $this->faker->numerify('###########'),
            'employee_type'          => $this->faker->randomElement(['ATS', 'ATS_Zona', 'Auxiliar', 'Celador', 'Administrativo']),
        ];
    }
}