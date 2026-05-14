<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TugasTest extends TestCase
{
    use RefreshDatabase;

    private function salesUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('sales');
        return $user;
    }

    private function teknisiUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('teknisi');
        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_sales_can_view_tugas_index(): void
    {
        $this->actingAs($this->salesUser())
             ->get(route('tugas.index'))
             ->assertOk();
    }

    public function test_teknisi_can_view_tugas_index(): void
    {
        $this->actingAs($this->teknisiUser())
             ->get(route('tugas.index'))
             ->assertOk();
    }

    public function test_sales_can_create_tugas(): void
    {
        $sales    = $this->salesUser();
        $customer = Customer::factory()->create();
        $teknisi  = $this->teknisiUser();

        $this->actingAs($sales)
             ->post(route('tugas.store'), [
                 'title'       => 'Test Tugas',
                 'customer_id' => $customer->id,
                 'assigned_to' => $teknisi->id,
             ])
             ->assertRedirect(route('tugas.index'));

        $this->assertDatabaseHas('tasks', ['title' => 'Test Tugas', 'created_by' => $sales->id]);
    }

    public function test_teknisi_cannot_create_tugas(): void
    {
        $teknisi  = $this->teknisiUser();
        $customer = Customer::factory()->create();

        $this->actingAs($teknisi)
             ->post(route('tugas.store'), [
                 'title'       => 'Unauthorized Tugas',
                 'customer_id' => $customer->id,
                 'assigned_to' => $teknisi->id,
             ])
             ->assertForbidden();
    }

    public function test_sales_can_delete_tugas(): void
    {
        $sales   = $this->salesUser();
        $teknisi = $this->teknisiUser();
        $task    = Task::factory()->create(['created_by' => $sales->id, 'assigned_to' => $teknisi->id]);

        $this->actingAs($sales)
             ->delete(route('tugas.destroy', $task))
             ->assertRedirect(route('tugas.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $this->get(route('tugas.index'))
             ->assertRedirect(route('login'));
    }
}
