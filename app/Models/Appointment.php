<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'doctor_name', 'specialty', 'scheduled_at',
    'location', 'image_url', 'notes', 'reminder_sent',
])]
class Appointment extends Model
{
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'reminder_sent' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture();
    }

    public function needsReminder(): bool
    {
        return $this->isUpcoming()
            && ! $this->reminder_sent
            && $this->scheduled_at->lte(now()->addDay());
    }

    public function getDisplayImageAttribute(): string
    {
        return $this->image_url ?: config('health_images.hospital');
    }
}
