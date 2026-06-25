<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'type', 'value', 'photo_path', 'recorded_at', 'notes'])]
class ProgressSnapshot extends Model
{
    public const TYPES = [
        'weight' => 'Kilo',
        'hba1c' => 'HbA1c',
        'waist' => 'Bel',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'recorded_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
