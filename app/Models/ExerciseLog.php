<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'type', 'duration_minutes', 'steps', 'logged_at', 'notes'])]
class ExerciseLog extends Model
{
    public const TYPES = [
        'walking' => 'Yürüyüş',
        'running' => 'Koşu',
        'cycling' => 'Bisiklet',
        'swimming' => 'Yüzme',
        'gym' => 'Spor salonu',
        'other' => 'Diğer',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
