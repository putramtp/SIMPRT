<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'    => fake()->company(),
            'phone'   => fake()->phoneNumber(),
            'email'   => fake()->unique()->companyEmail(),
            'address' => fake()->address(),
        ];
    }
}
