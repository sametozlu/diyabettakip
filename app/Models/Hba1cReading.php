<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'value', 'tested_at', 'notes'])]
class Hba1cReading extends Model
{
    protected function casts(): array
    {
        return [
            'value' => 'decimal:1',
            'tested_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): string
    {
        if ($this->value < 5.7) {
            return 'normal';
        }

        if ($this->value < 6.5) {
            return 'prediabetes';
        }

        return 'high';
    }

    public function statusLabel(): string
    {
        return match ($this->status()) {
            'normal' => 'Normal',
            'prediabetes' => 'Prediyabet',
            default => 'Yüksek',
        };
    }
}
