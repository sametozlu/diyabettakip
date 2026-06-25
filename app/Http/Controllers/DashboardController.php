<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Services\HealthAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->healthProfile;
        $targetMin = $profile?->target_min ?? 70;
        $targetMax = $profile?->target_max ?? 140;

        $readings = $user->bloodSugarReadings()
            ->latest('measured_at')
            ->limit(8)
            ->get();

        $weekReadings = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(6)->startOfDay())
            ->orderBy('measured_at')
            ->get();

        $weeklyChart = $weekReadings
            ->groupBy(fn ($r) => $r->measured_at->format('Y-m-d'))
            ->map(fn ($day) => round($day->avg('value'), 1));

        $weeklyDetail = collect(range(6, 0))->mapWithKeys(function ($daysAgo) use ($user, $targetMin, $targetMax) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $day = $user->bloodSugarReadings()
                ->whereDate('measured_at', $date)
                ->get();

            return [$date => [
                'label' => Carbon::parse($date)->locale('tr')->isoFormat('ddd'),
                'date' => Carbon::parse($date)->format('d M'),
                'avg' => $day->count() ? round($day->avg('value'), 1) : null,
                'min' => $day->count() ? round($day->min('value'), 1) : null,
                'max' => $day->count() ? round($day->max('value'), 1) : null,
                'count' => $day->count(),
                'in_range' => $day->count()
                    ? round($day->filter(fn ($r) => $r->value >= $targetMin && $r->value <= $targetMax)->count() / $day->count() * 100)
                    : null,
            ]];
        });

        $last7 = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(7))
            ->get();

        $prev7 = $user->bloodSugarReadings()
            ->whereBetween('measured_at', [now()->subDays(14), now()->subDays(7)])
            ->get();

        $avg7 = round($last7->avg('value') ?? 0, 1);
        $avgPrev = round($prev7->avg('value') ?? 0, 1);
        $inRangePercent = $last7->count()
            ? round($last7->filter(fn ($r) => $r->value >= $targetMin && $r->value <= $targetMax)->count() / $last7->count() * 100)
            : 0;

        $todayMeal = MealPlan::forUser($user->id)
            ->whereDate('plan_date', today())
            ->first();

        $upcomingAppointments = $user->appointments()
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(4)
            ->get();

        $reminders = $user->appointments()
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDay())
            ->where('reminder_sent', false)
            ->orderBy('scheduled_at')
            ->get();

        $lastHba1c = $user->hba1cReadings()->latest('tested_at')->first();

        $stats = [
            'avg_7d' => $avg7,
            'avg_prev' => $avgPrev,
            'trend' => $avgPrev > 0 ? round($avg7 - $avgPrev, 1) : null,
            'last' => $readings->first()?->value,
            'last_status' => $readings->first()?->status(),
            'total_readings' => $user->bloodSugarReadings()->count(),
            'readings_7d' => $last7->count(),
            'in_range_percent' => $inRangePercent,
            'upcoming_count' => $user->appointments()->where('scheduled_at', '>=', now())->count(),
            'last_hba1c' => $lastHba1c?->value,
            'hba1c_status' => $lastHba1c?->statusLabel(),
            'active_meds' => $user->medications()->where('is_active', true)->count(),
        ];

        $activeMedications = $user->medications()->where('is_active', true)->orderBy('name')->get();

        $alerts = $this->analytics->glucoseAlerts($user);
        $estimatedHbA1c = $this->analytics->estimatedHbA1c($user);
        $todayWater = $user->waterLogs()->whereDate('logged_at', today())->sum('amount_ml');
        $waterGoal = $profile?->water_goal_ml ?? 2000;
        $mealInsights = $this->analytics->mealGlucoseInsights($user);
        $achievements = $this->analytics->achievements($user);

        return view('dashboard', compact(
            'profile', 'readings', 'weeklyChart', 'weeklyDetail', 'todayMeal',
            'upcomingAppointments', 'reminders', 'stats', 'activeMedications',
            'targetMin', 'targetMax', 'alerts', 'estimatedHbA1c', 'todayWater', 'waterGoal',
            'mealInsights', 'achievements'
        ));
    }
}
