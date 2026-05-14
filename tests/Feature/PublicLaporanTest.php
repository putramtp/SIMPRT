<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PublicLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_valid_signed_url_shows_customer_laporan(): void
    {
        $customer = Customer::factory()->create();

        $url = URL::temporarySignedRoute(
            'customers.public-laporan',
            now()->addDays(30),
            ['customer' => $customer->id]
        );

        $this->get($url)->assertOk();
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $customer = Customer::factory()->create();

        $this->get(route('customers.public-laporan', $customer) . '?signature=invalid')
             ->assertForbidden();
    }

    public function test_expired_signed_url_is_rejected(): void
    {
        $customer = Customer::factory()->create();

        $url = URL::temporarySignedRoute(
            'customers.public-laporan',
            now()->subSecond(),
            ['customer' => $customer->id]
        );

        $this->get($url)->assertForbidden();
    }
}
