<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'target_min', 'target_max', 'weight',
    'height', 'diabetes_type', 'doctor_name',
    'water_goal_ml', 'daily_steps_goal',
])]
class HealthProfile extends Model
{
    public const DIABETES_TYPES = [
        'tip1' => 'Tip 1',
        'tip2' => 'Tip 2',
        'gestational' => 'Gestasyonel',
        'prediabetes' => 'Prediyabet',
        'other' => 'Diğer',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:1',
            'height' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bmi(): ?float
    {
        if (! $this->weight || ! $this->height || $this->height <= 0) {
            return null;
        }

        return round($this->weight / ($this->height ** 2), 1);
    }

    public function bmiCategory(): ?string
    {
        $bmi = $this->bmi();
        if ($bmi === null) {
            return null;
        }

        return match (true) {
            $bmi < 18.5 => __('Zayıf'),
            $bmi < 25 => __('Normal'),
            $bmi < 30 => __('Fazla kilolu'),
            default => __('Obez'),
        };
    }
}
