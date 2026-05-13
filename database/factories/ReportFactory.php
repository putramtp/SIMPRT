<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        $task = Task::inRandomOrder()->first() ?? Task::factory()->create();

        return [
            'task_id'     => $task->id,
            'user_id'     => $task->assigned_to,
            'description' => fake()->paragraphs(2, true),
            'status'      => fake()->randomElement(['draft', 'submitted', 'approved']),
            'photo'       => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(['status' => 'submitted']);
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }
}
