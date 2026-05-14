<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private function teknisiUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('teknisi');
        return $user;
    }

    private function salesUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('sales');
        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_teknisi_can_submit_laporan(): void
    {
        $teknisi  = $this->teknisiUser();
        $customer = Customer::factory()->create();
        $task     = Task::factory()->create(['assigned_to' => $teknisi->id, 'customer_id' => $customer->id]);

        $this->actingAs($teknisi)
             ->post(route('laporan.store'), [
                 'task_id'     => $task->id,
                 'description' => 'Pekerjaan selesai dilakukan.',
             ])
             ->assertRedirect(route('laporan.index'));

        $this->assertDatabaseHas('reports', [
            'task_id' => $task->id,
            'user_id' => $teknisi->id,
            'status'  => 'submitted',
        ]);
    }

    public function test_non_teknisi_cannot_submit_laporan(): void
    {
        $sales    = $this->salesUser();
        $customer = Customer::factory()->create();
        $task     = Task::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($sales)
             ->post(route('laporan.store'), [
                 'task_id'     => $task->id,
                 'description' => 'Unauthorized.',
             ])
             ->assertForbidden();
    }

    public function test_teknisi_cannot_edit_another_teknisi_laporan(): void
    {
        $owner   = $this->teknisiUser();
        $other   = $this->teknisiUser();
        $customer = Customer::factory()->create();
        $task    = Task::factory()->create(['assigned_to' => $owner->id, 'customer_id' => $customer->id]);
        $report  = Report::factory()->create(['task_id' => $task->id, 'user_id' => $owner->id]);

        $this->actingAs($other)
             ->get(route('laporan.edit', $report))
             ->assertForbidden();
    }

    public function test_owner_can_edit_own_laporan(): void
    {
        $teknisi  = $this->teknisiUser();
        $customer = Customer::factory()->create();
        $task     = Task::factory()->create(['assigned_to' => $teknisi->id, 'customer_id' => $customer->id]);
        $report   = Report::factory()->create(['task_id' => $task->id, 'user_id' => $teknisi->id]);

        $this->actingAs($teknisi)
             ->get(route('laporan.edit', $report))
             ->assertOk();
    }

    public function test_teknisi_cannot_delete_another_teknisi_laporan(): void
    {
        $owner    = $this->teknisiUser();
        $other    = $this->teknisiUser();
        $customer = Customer::factory()->create();
        $task     = Task::factory()->create(['assigned_to' => $owner->id, 'customer_id' => $customer->id]);
        $report   = Report::factory()->create(['task_id' => $task->id, 'user_id' => $owner->id]);

        $this->actingAs($other)
             ->delete(route('laporan.destroy', $report))
             ->assertForbidden();
    }

    public function test_sales_can_delete_any_laporan(): void
    {
        $sales    = $this->salesUser();
        $teknisi  = $this->teknisiUser();
        $customer = Customer::factory()->create();
        $task     = Task::factory()->create(['assigned_to' => $teknisi->id, 'customer_id' => $customer->id]);
        $report   = Report::factory()->create(['task_id' => $task->id, 'user_id' => $teknisi->id]);

        $this->actingAs($sales)
             ->delete(route('laporan.destroy', $report))
             ->assertRedirect(route('laporan.index'));

        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }
}
