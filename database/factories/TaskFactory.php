<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
            'assigned_to' => User::role('teknisi')->inRandomOrder()->first()?->id ?? User::factory(),
            'created_by'  => User::role('sales')->inRandomOrder()->first()?->id ?? User::factory(),
            'status'      => fake()->randomElement(['pending', 'in_progress', 'completed']),
            'due_date'    => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }
}
