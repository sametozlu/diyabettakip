<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'plan_date', 'day_name', 'week_label',
    'menu_items', 'image_url', 'eat_items', 'reduce_items', 'skip_items',
])]
class MealPlan extends Model
{
    protected $appends = ['display_image'];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'eat_items' => 'array',
            'reduce_items' => 'array',
            'skip_items' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->whereNull('user_id')->orWhere('user_id', $userId);
        });
    }

    public function getDisplayImageAttribute(): string
    {
        return $this->image_url ?: \App\Helpers\HealthImages::mealFor($this->menu_items ?? '');
    }
}
