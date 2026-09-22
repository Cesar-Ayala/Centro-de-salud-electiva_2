<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'name'                   => $this->faker->name(),
            'address'                => $this->faker->streetAddress(),
            'phone'                  => $this->faker->numerify('##########'),
            'postal_code'            => $this->faker->postcode(),
            'nif'                    => strtoupper($this->faker->bothify('########?')),
            'social_security_number' => $this->faker->numerify('###########'),
            'doctor_id'              => Doctor::inRandomOrder()->first()?->id ?? Doctor::factory(),
        ];
    }
}