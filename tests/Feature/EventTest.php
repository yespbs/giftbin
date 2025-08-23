<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_events(): void
    {
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'location',
                        'start_date',
                        'end_date',
                        'price',
                        'max_participants',
                        'status',
                        'category',
                        'user',
                    ]
                ],
                'current_page',
                'per_page',
                'total'
            ]);
    }

    public function test_can_show_single_event(): void
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'location',
                'start_date',
                'end_date',
                'price',
                'max_participants',
                'status',
                'category',
                'user',
            ])
            ->assertJson([
                'id' => $event->id,
                'title' => $event->title,
            ]);
    }

    public function test_authenticated_user_can_create_event(): void
    {
        Sanctum::actingAs($this->user);

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test event description',
            'location' => 'Test Location',
            'start_date' => now()->addDays(7)->toISOString(),
            'end_date' => now()->addDays(7)->addHours(2)->toISOString(),
            'price' => 99.99,
            'max_participants' => 50,
            'status' => 'published',
            'category' => 'Technology',
        ];

        $response = $this->postJson('/api/events', $eventData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Event',
                'location' => 'Test Location',
            ]);

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_event(): void
    {
        $eventData = [
            'title' => 'Test Event',
            'start_date' => now()->addDays(7)->toISOString(),
        ];

        $response = $this->postJson('/api/events', $eventData);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_update_own_event(): void
    {
        Sanctum::actingAs($this->user);

        $event = Event::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Event Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/events/{$event->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Event Title',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
        ]);
    }

    public function test_user_cannot_update_others_event(): void
    {
        $otherUser = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($this->user);

        $updateData = ['title' => 'Hacked Title'];

        $response = $this->putJson("/api/events/{$event->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_delete_own_event(): void
    {
        Sanctum::actingAs($this->user);

        $event = Event::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }

    public function test_user_cannot_delete_others_event(): void
    {
        $otherUser = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
        ]);
    }

    public function test_can_filter_events_by_status(): void
    {
        Event::factory()->create(['status' => 'published']);
        Event::factory()->create(['status' => 'draft']);

        $response = $this->getJson('/api/events?status=published');

        $response->assertStatus(200);
        $this->assertEquals('published', $response->json('data.0.status'));
    }

    public function test_can_search_events_by_title(): void
    {
        Event::factory()->create(['title' => 'Laravel Conference 2024']);
        Event::factory()->create(['title' => 'React Workshop']);

        $response = $this->getJson('/api/events?search=Laravel');

        $response->assertStatus(200);
        $this->assertStringContainsString('Laravel', $response->json('data.0.title'));
    }

    public function test_create_event_validation(): void
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'title' => '', // Required field empty
            'start_date' => 'invalid-date', // Invalid date format
            'price' => -10, // Negative price
        ];

        $response = $this->postJson('/api/events', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'start_date', 'price']);
    }
}
