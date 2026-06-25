<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'carbs_grams', 'insulin_units', 'meal_type', 'logged_at', 'notes'])]
class InsulinLog extends Model
{
    public const MEAL_TYPES = [
        'breakfast' => 'Kahvaltı',
        'lunch' => 'Öğle',
        'dinner' => 'Akşam',
        'snack' => 'Ara öğün',
        'other' => 'Diğer',
    ];

    protected function casts(): array
    {
        return [
            'carbs_grams' => 'decimal:1',
            'insulin_units' => 'decimal:1',
            'logged_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
