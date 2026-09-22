<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

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
            'collegiate_number'      => $this->faker->numerify('#####'),
            'doctor_type'            => $this->faker->randomElement(['Titular', 'Sustituto', 'Interino']),
            'hiring_date'            => $this->faker->date(),
            'discharge_date'         => null,
        ];
    }
}