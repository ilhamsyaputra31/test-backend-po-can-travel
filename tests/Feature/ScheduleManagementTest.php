<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ApiKey;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $bus;
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();
        
        ApiKey::create([
            'app_name' => 'Test App',
            'api_key' => 'test_api_key',
            'status' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'role' => 'admin',
        ]);

        $this->bus = Bus::create([
            'bus_code' => 'BUS001',
            'bus_name' => 'Express',
            'seat_capacity' => 40,
            'class' => 'executive',
        ]);

        $this->route = Route::create([
            'origin' => 'Jakarta',
            'destination' => 'Bandung',
            'distance_km' => 150,
        ]);
    }

    public function test_admin_can_list_all_schedules(): void
    {
        Schedule::create([
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(3),
            'price' => 100000,
            'available_seats' => 40,
        ]);

        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/schedules');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_admin_can_create_schedule(): void
    {
        $payload = [
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'arrival_time' => now()->addDays(2)->addHours(4)->format('Y-m-d H:i:s'),
            'price' => 120000,
            'available_seats' => 30,
        ];

        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/schedules', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.price', "120000.00");
        
        $this->assertDatabaseHas('schedules', ['price' => 120000]);
    }

    public function test_admin_can_update_schedule(): void
    {
        $schedule = Schedule::create([
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(3),
            'price' => 100000,
            'available_seats' => 40,
        ]);

        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->actingAs($this->admin, 'sanctum')
            ->putJson("/api/admin/schedules/{$schedule->id}", [
                'price' => 150000
            ]);

        $response->assertStatus(200);
        $this->assertEquals(150000, $schedule->fresh()->price);
    }

    public function test_admin_can_delete_schedule(): void
    {
        $schedule = Schedule::create([
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(3),
            'price' => 100000,
            'available_seats' => 40,
        ]);

        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/admin/schedules/{$schedule->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}
