<?php

namespace Tests\Feature;

use App\Events\EventPublished;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Broadcasting\Channel;
use Tests\TestCase;

class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_event_published_broadcast_uses_correct_channel_and_event_name(): void
    {
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published'
        ]);

        $broadcast = new EventPublished($event->load('user'));
        
        $channels = $broadcast->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(Channel::class, $channels[0]);
        $this->assertEquals('events', $channels[0]->name);
        
        $this->assertEquals('event.published', $broadcast->broadcastAs());
    }

    public function test_event_published_broadcast_contains_correct_data(): void
    {
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => 'Test Event',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'category' => 'Technology',
            'price' => 99.99
        ]);

        $broadcast = new EventPublished($event->load('user'));
        $broadcastData = $broadcast->broadcastWith();
        
        $this->assertEquals($event->id, $broadcastData['id']);
        $this->assertEquals('Test Event', $broadcastData['title']);
        $this->assertEquals('Test Description', $broadcastData['description']);
        $this->assertEquals('Test Location', $broadcastData['location']);
        $this->assertEquals('Technology', $broadcastData['category']);
        $this->assertEquals(99.99, $broadcastData['price']);
        $this->assertEquals($this->user->id, $broadcastData['user']['id']);
        $this->assertEquals($this->user->name, $broadcastData['user']['name']);
    }

    public function test_event_model_broadcasts_when_created_with_published_status(): void
    {
        $broadcastCalled = false;
        
        $this->app->bind('Illuminate\Broadcasting\BroadcastEvent', function ($app, $parameters) use (&$broadcastCalled) {
            $event = $parameters[0];
            if ($event instanceof EventPublished) {
                $broadcastCalled = true;
            }
            return new \Illuminate\Broadcasting\BroadcastEvent($event);
        });

        Event::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published'
        ]);

        $this->assertTrue($broadcastCalled, 'EventPublished broadcast should be dispatched when event is created with published status');
    }

    public function test_event_model_broadcasts_when_status_updated_to_published(): void
    {
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft'
        ]);

        $broadcastCalled = false;
        
        $this->app->bind('Illuminate\Broadcasting\BroadcastEvent', function ($app, $parameters) use (&$broadcastCalled) {
            $event = $parameters[0];
            if ($event instanceof EventPublished) {
                $broadcastCalled = true;
            }
            return new \Illuminate\Broadcasting\BroadcastEvent($event);
        });

        $event->update(['status' => 'published']);

        $this->assertTrue($broadcastCalled, 'EventPublished broadcast should be dispatched when event status is updated to published');
    }

    public function test_event_model_does_not_broadcast_when_created_with_draft_status(): void
    {
        $broadcastCalled = false;
        
        $this->app->bind('Illuminate\Broadcasting\BroadcastEvent', function ($app, $parameters) use (&$broadcastCalled) {
            $event = $parameters[0];
            if ($event instanceof EventPublished) {
                $broadcastCalled = true;
            }
            return new \Illuminate\Broadcasting\BroadcastEvent($event);
        });

        Event::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft'
        ]);

        $this->assertFalse($broadcastCalled, 'EventPublished broadcast should NOT be dispatched when event is created with draft status');
    }
}