<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TechnicianFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'specialization' => fake()->randomElement([
                'Instalasi Listrik', 'Jaringan & Internet', 'CCTV & Keamanan',
                'AC & Pendingin', 'Perbaikan Perangkat', 'Sistem Informasi',
            ]),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
