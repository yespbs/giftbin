<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventPublished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Event $event)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('events'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->event->id,
            'title' => $this->event->title,
            'description' => $this->event->description,
            'location' => $this->event->location,
            'start_date' => $this->event->start_date,
            'end_date' => $this->event->end_date,
            'price' => $this->event->price,
            'status' => $this->event->status,
            'category' => $this->event->category,
            'user' => [
                'id' => $this->event->user->id,
                'name' => $this->event->user->name,
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'event.published';
    }
}
