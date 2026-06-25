<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'value', 'context', 'measured_at', 'notes', 'mood', 'sleep_hours', 'stress_level'])]
class BloodSugarReading extends Model
{
    public const CONTEXTS = [
        'fasting' => 'Açlık',
        'before_meal' => 'Yemek Öncesi',
        'after_meal' => 'Yemek Sonrası',
        'bedtime' => 'Yatmadan Önce',
        'other' => 'Diğer',
    ];

    public const MOODS = [
        'good' => 'İyi',
        'tired' => 'Yorgun',
        'stressed' => 'Stresli',
        'sick' => 'Hasta',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:1',
            'measured_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): string
    {
        $profile = $this->user->healthProfile;
        $min = $profile?->target_min ?? 70;
        $max = $profile?->target_max ?? 140;

        if ($this->value < $min) {
            return 'low';
        }

        if ($this->value > $max) {
            return 'high';
        }

        return 'normal';
    }
}
