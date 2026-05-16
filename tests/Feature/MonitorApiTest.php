<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Only CREATE the user here, don't authenticate yet
        $this->user = User::factory()->create();
    }

    // Helper to authenticate when needed
    private function authenticate(): void
    {
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_create_monitor(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/monitors', [
            'url' => 'https://example.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.url', 'https://example.com')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.check_interval', 5)
            ->assertJsonPath('data.threshold', 3);
    }

    public function test_duplicate_url_is_rejected(): void
    {
        $this->authenticate();

        Monitor::factory()->create([
            'user_id' => $this->user->id,
            'url'     => 'https://example.com',
        ]);

        $response = $this->postJson('/api/monitors', [
            'url' => 'https://example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_url_is_required(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/monitors', []);

        $response->assertStatus(422)
            ->assertJsonPath('errors.url.0', 'The url field is required.');
    }

    public function test_can_list_monitors(): void
    {
        $this->authenticate();

        Monitor::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/monitors');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_cannot_see_other_users_monitors(): void
    {
        $this->authenticate();

        $otherUser = User::factory()->create();
        Monitor::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/monitors');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_can_get_check_history(): void
    {
        $this->authenticate();

        $monitor = Monitor::factory()->create(['user_id' => $this->user->id]);
        MonitorCheck::factory()->count(5)->create(['monitor_id' => $monitor->id]);

        $response = $this->getJson("/api/monitors/{$monitor->id}/history");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'per_page', 'total']]);
    }

    public function test_history_returns_404_for_missing_monitor(): void
    {
        $this->authenticate();

        $response = $this->getJson('/api/monitors/999/history');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Monitor not found.']);
    }

    public function test_unauthenticated_user_cannot_access_monitors(): void
    {
        // No authenticate() call here — request is made as a guest
        $response = $this->getJson('/api/monitors');

        $response->assertStatus(401);
    }
}