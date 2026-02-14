<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $apiKey;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->apiKey = ApiKey::create([
            'app_name' => 'Test App',
            'api_key' => 'test_api_key',
            'status' => true,
        ]);
    }

    /**
     * Test admin can access admin endpoints.
     */
    public function test_admin_can_access_admin_orders(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'role' => 'admin',
        ]);

        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/orders');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test customer cannot access admin endpoints.
     */
    public function test_customer_cannot_access_admin_orders(): void
    {
        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'role' => 'customer',
        ]);

        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->actingAs($customer, 'sanctum')
            ->getJson('/api/admin/orders');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ]);
    }
}
