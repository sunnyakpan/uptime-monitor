<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_monitor(): void
    {
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
        Monitor::factory()->create(['url' => 'https://example.com']);

        $response = $this->postJson('/api/monitors', [
            'url' => 'https://example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_url_is_required(): void
    {
        $response = $this->postJson('/api/monitors', []);

        $response->assertStatus(422)
            ->assertJsonPath('errors.url.0', 'The url field is required.');
    }

    public function test_can_list_monitors(): void
    {
        Monitor::factory()->count(3)->create();

        $response = $this->getJson('/api/monitors');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_check_history(): void
    {
        $monitor = Monitor::factory()->create();
        MonitorCheck::factory()->count(5)->create(['monitor_id' => $monitor->id]);

        $response = $this->getJson("/api/monitors/{$monitor->id}/history");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'per_page', 'total']]);
    }

    public function test_history_returns_404_for_missing_monitor(): void
    {
        $response = $this->getJson('/api/monitors/999/history');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Monitor not found.']);
    }
}