<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'dosage', 'frequency', 'times', 'notes', 'is_active'])]
class Medication extends Model
{
    public const FREQUENCIES = [
        'daily' => 'Günlük',
        'twice_daily' => 'Günde 2 kez',
        'three_daily' => 'Günde 3 kez',
        'weekly' => 'Haftalık',
        'as_needed' => 'Gerektiğinde',
    ];

    protected function casts(): array
    {
        return [
            'times' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function frequencyLabel(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? $this->frequency;
    }
}
