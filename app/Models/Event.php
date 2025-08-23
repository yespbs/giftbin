<?php

namespace App\Models;

use App\Events\EventPublished;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'price',
        'max_participants',
        'status',
        'category',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    protected static function booted()
    {
        static::updated(function (Event $event) {
            if ($event->isDirty('status') && $event->status === 'published') {
                broadcast(new EventPublished($event->load('user')));
            }
        });

        static::created(function (Event $event) {
            if ($event->status === 'published') {
                broadcast(new EventPublished($event->load('user')));
            }
        });
    }
}
