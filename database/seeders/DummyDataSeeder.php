<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Report;
use App\Models\Task;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed admin account
        $admin = User::firstOrCreate(
            ['email' => 'admin@siprt.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        $sales = User::firstOrCreate(
            ['email' => 'sales@siprt.com'],
            ['name' => 'Sales User', 'password' => Hash::make('password')]
        );
        $sales->syncRoles(['sales']);

        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@siprt.com'],
            ['name' => 'Technician User', 'password' => Hash::make('password')]
        );
        $teknisi->syncRoles(['teknisi']);

        // 2 sales users
        $salesUsers = User::factory(2)->create()->each(function ($user) {
            $user->assignRole('sales');
        });

        // 5 teknisi users with technician profiles
        $teknisiUsers = User::factory(5)->create()->each(function ($user) {
            $user->assignRole('teknisi');
            Technician::factory()->create(['user_id' => $user->id]);
        });

        // 10 customers
        Customer::factory(10)->create();

        // 20 tasks spread across teknisi
        Task::factory(20)->create();

        // 1–2 reports per completed or in-progress task
        Task::whereIn('status', ['in_progress', 'completed'])->get()->each(function ($task) {
            $count = $task->status === 'completed' ? 2 : 1;
            Report::factory($count)->create([
                'task_id' => $task->id,
                'user_id' => $task->assigned_to,
            ]);
        });
    }
}
