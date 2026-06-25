<?php

namespace App\Helpers;

class HealthImages
{
    public static function get(string $key): string
    {
        return config("health_images.{$key}", '');
    }

    public static function mealFor(string $menuText): string
    {
        $text = mb_strtolower($menuText);
        foreach (config('health_images.meal_defaults', []) as $keyword => $url) {
            if ($keyword !== 'default' && str_contains($text, $keyword)) {
                return $url;
            }
        }

        return config('health_images.meal_defaults.default');
    }

    public static function badge(string $id): string
    {
        return config("health_images.badges.{$id}", '🏅');
    }
}
