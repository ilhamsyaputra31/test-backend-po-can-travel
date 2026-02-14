<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
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
     * Test that authentication routes are throttled.
     */
    public function test_login_is_throttled_after_10_attempts(): void
    {
        // First 10 attempts should pass (even if they fail validation/auth, the status won't be 429)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withHeader('X-API-Key', 'test_api_key')
                ->postJson('/api/login', [
                    'email' => 'wrong@test.com',
                    'password' => 'wrongpass'
                ]);
            
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 11th attempt should be throttled
        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->postJson('/api/login', [
                'email' => 'wrong@test.com',
                'password' => 'wrongpass'
            ]);

        $response->assertStatus(429);
    }

    /**
     * Test that general API routes are throttled (default is 60).
     */
    public function test_api_is_throttled_after_60_attempts(): void
    {
        // 60 attempts should pass
        for ($i = 0; $i < 60; $i++) {
            $response = $this->withHeader('X-API-Key', 'test_api_key')
                ->getJson('/api/schedules');
            
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 61st attempt should be throttled
        $response = $this->withHeader('X-API-Key', 'test_api_key')
            ->getJson('/api/schedules');

        $response->assertStatus(429);
    }
}
