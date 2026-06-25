<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HealthAnalyticsService
{
    public function estimatedHbA1c(User $user): ?float
    {
        $readings = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(90))
            ->get();

        if ($readings->count() < 5) {
            return null;
        }

        $avgGlucose = $readings->avg('value');

        return round(($avgGlucose + 46.7) / 28.7, 1);
    }

    public function averageGlucose90d(User $user): ?float
    {
        $avg = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(90))
            ->avg('value');

        return $avg ? round($avg, 1) : null;
    }

    /** @return Collection<int, array{type: string, severity: string, message: string}> */
    public function glucoseAlerts(User $user): Collection
    {
        $alerts = collect();
        $profile = $user->healthProfile;
        $targetMin = $profile?->target_min ?? 70;
        $targetMax = $profile?->target_max ?? 140;

        $evening = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(3))
            ->whereIn('context', ['after_meal', 'bedtime'])
            ->get();

        if ($evening->count() >= 3) {
            $highEvening = $evening->filter(fn ($r) => $r->value > $targetMax)->count();
            if ($highEvening >= 2) {
                $alerts->push([
                    'type' => 'evening_high',
                    'severity' => 'warning',
                    'message' => __('Son 3 günde akşam ölçümleriniz sık sık hedefin üzerinde.'),
                ]);
            }
        }

        $last7 = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(7))
            ->get();

        if ($last7->count() >= 3) {
            $outOfRange = $last7->filter(fn ($r) => $r->value < $targetMin || $r->value > $targetMax)->count();
            $pct = round($outOfRange / $last7->count() * 100);
            if ($pct >= 50) {
                $alerts->push([
                    'type' => 'out_of_range',
                    'severity' => 'danger',
                    'message' => __('Son 7 günde ölçümlerinizin %:pct kadarı hedef aralık dışında.', ['pct' => $pct]),
                ]);
            }
        }

        $lowReadings = $last7->filter(fn ($r) => $r->value < $targetMin);
        if ($lowReadings->count() >= 2) {
            $alerts->push([
                'type' => 'hypo_risk',
                'severity' => 'warning',
                'message' => __('Düşük glukoz ölçümleri tespit edildi. Doktorunuza danışın.'),
            ]);
        }

        $estimated = $this->estimatedHbA1c($user);
        if ($estimated && $estimated >= 7.0) {
            $alerts->push([
                'type' => 'hba1c_estimate',
                'severity' => 'info',
                'message' => __('Tahmini HbA1c: :value — kontrol önerilir.', ['value' => $estimated]),
            ]);
        }

        $todayWater = $user->waterLogs()->whereDate('logged_at', today())->sum('amount_ml');
        $waterGoal = $profile?->water_goal_ml ?? 2000;
        if ($todayWater > 0 && $todayWater < $waterGoal * 0.4 && now()->hour >= 14) {
            $alerts->push([
                'type' => 'water_low',
                'severity' => 'info',
                'message' => __('Bugün su hedefinizin altındasınız (%:pct).', [
                    'pct' => round($todayWater / $waterGoal * 100),
                ]),
            ]);
        }

        return $alerts;
    }

    /** @return Collection<int, array{context: string, label: string, avg: float, count: int, spike: float|null, insight: string}> */
    public function mealGlucoseInsights(User $user): Collection
    {
        $profile = $user->healthProfile;
        $targetMax = $profile?->target_max ?? 140;
        $labels = \App\Models\BloodSugarReading::CONTEXTS;

        $readings = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(30))
            ->whereIn('context', ['fasting', 'before_meal', 'after_meal', 'bedtime'])
            ->get()
            ->groupBy('context');

        $fastingAvg = $readings->get('fasting')?->avg('value');

        return $readings->map(function ($group, $context) use ($labels, $fastingAvg, $targetMax) {
            $avg = round($group->avg('value'), 1);
            $spike = null;
            $insight = __('Ortalama :avg mg/dL', ['avg' => $avg]);

            if ($context === 'after_meal' && $fastingAvg) {
                $spike = round($avg - $fastingAvg, 1);
                if ($spike > 50) {
                    $insight = __('Yemek sonrası glukoz :spike mg/dL yükseliyor — porsiyon kontrolü önerilir.', ['spike' => $spike]);
                } elseif ($avg > $targetMax) {
                    $insight = __('Yemek sonrası ortalama hedefin üzerinde (:avg mg/dL).', ['avg' => $avg]);
                }
            } elseif ($context === 'fasting' && $avg > $targetMax) {
                $insight = __('Açlık glukozunuz yüksek — doktorunuza danışın.');
            }

            return [
                'context' => $context,
                'label' => $labels[$context] ?? $context,
                'avg' => $avg,
                'count' => $group->count(),
                'spike' => $spike,
                'insight' => $insight,
            ];
        })->values();
    }

    /** @return Collection<int, array{id: string, title: string, description: string, earned: bool}> */
    public function achievements(User $user): Collection
    {
        $last7 = $user->bloodSugarReadings()->where('measured_at', '>=', now()->subDays(7))->get();
        $targetMin = $user->healthProfile?->target_min ?? 70;
        $targetMax = $user->healthProfile?->target_max ?? 140;
        $inRange = $last7->count()
            ? $last7->filter(fn ($r) => $r->value >= $targetMin && $r->value <= $targetMax)->count() / $last7->count()
            : 0;

        $waterDays = 0;
        for ($i = 0; $i < 7; $i++) {
            $total = $user->waterLogs()->whereDate('logged_at', today()->subDays($i))->sum('amount_ml');
            $goal = $user->healthProfile?->water_goal_ml ?? 2000;
            if ($total >= $goal) {
                $waterDays++;
            }
        }

        $streak = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(14))
            ->get()
            ->groupBy(fn ($r) => $r->measured_at->format('Y-m-d'))
            ->keys()
            ->count();

        return collect([
            ['id' => 'readings_7d', 'title' => '7 Gün Ölçüm', 'description' => 'Bu hafta 5+ ölçüm', 'earned' => $last7->count() >= 5],
            ['id' => 'in_range', 'title' => 'Hedef Avcısı', 'description' => '%70+ hedefte', 'earned' => $inRange >= 0.7 && $last7->count() >= 3],
            ['id' => 'water', 'title' => 'Su Ustası', 'description' => '3 gün su hedefi', 'earned' => $waterDays >= 3],
            ['id' => 'streak', 'title' => 'Düzenli Takip', 'description' => '10+ günlük kayıt', 'earned' => $streak >= 10],
        ]);
    }

    /** @return array<string, mixed> */
    public function weeklyReport(User $user): array
    {
        $readings = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(7))
            ->get();

        $profile = $user->healthProfile;
        $targetMin = $profile?->target_min ?? 70;
        $targetMax = $profile?->target_max ?? 140;

        return [
            'period' => now()->subDays(7)->format('d.m.Y').' — '.now()->format('d.m.Y'),
            'readings_count' => $readings->count(),
            'avg_glucose' => $readings->count() ? round($readings->avg('value'), 1) : null,
            'in_range_percent' => $readings->count()
                ? round($readings->filter(fn ($r) => $r->value >= $targetMin && $r->value <= $targetMax)->count() / $readings->count() * 100)
                : 0,
            'water_total' => $user->waterLogs()->where('logged_at', '>=', now()->subDays(7))->sum('amount_ml'),
            'exercise_minutes' => $user->exerciseLogs()->where('logged_at', '>=', now()->subDays(7))->sum('duration_minutes'),
            'estimated_hba1c' => $this->estimatedHbA1c($user),
            'meal_insights' => $this->mealGlucoseInsights($user)->take(3),
            'alerts' => $this->glucoseAlerts($user),
        ];
    }

    /** @return array<string, mixed> */
    public function dailySummary(User $user): array
    {
        $profile = $user->healthProfile;
        $targetMin = $profile?->target_min ?? 70;
        $targetMax = $profile?->target_max ?? 140;

        $todayReadings = $user->bloodSugarReadings()->whereDate('measured_at', today())->get();
        $todayMeal = \App\Models\MealPlan::forUser($user->id)->whereDate('plan_date', today())->first();
        $todayMeds = $user->medications()->where('is_active', true)->get();
        $todayWater = $user->waterLogs()->whereDate('logged_at', today())->sum('amount_ml');
        $todayExercise = $user->exerciseLogs()->whereDate('logged_at', today())->get();
        $todayInsulin = $user->insulinLogs()->whereDate('logged_at', today())->get();
        $upcomingAppt = $user->appointments()->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->first();

        $now = now()->format('H:i');
        $pendingMeds = $todayMeds->filter(function ($med) use ($now) {
            return collect($med->times ?? [])->contains(fn ($t) => $t >= $now);
        });

        return [
            'date' => today(),
            'readings_count' => $todayReadings->count(),
            'readings_avg' => $todayReadings->count() ? round($todayReadings->avg('value'), 1) : null,
            'readings_in_range' => $todayReadings->count()
                ? round($todayReadings->filter(fn ($r) => $r->value >= $targetMin && $r->value <= $targetMax)->count() / $todayReadings->count() * 100)
                : null,
            'last_reading' => $todayReadings->sortByDesc('measured_at')->first(),
            'meal' => $todayMeal,
            'water_ml' => $todayWater,
            'water_goal' => $profile?->water_goal_ml ?? 2000,
            'water_percent' => round($todayWater / ($profile?->water_goal_ml ?? 2000) * 100),
            'exercise_minutes' => $todayExercise->sum('duration_minutes'),
            'exercise_steps' => $todayExercise->sum('steps'),
            'steps_goal' => $profile?->daily_steps_goal ?? 8000,
            'insulin_logs' => $todayInsulin->count(),
            'total_carbs' => round($todayInsulin->sum('carbs_grams'), 1),
            'total_insulin' => round($todayInsulin->sum('insulin_units'), 1),
            'pending_meds' => $pendingMeds,
            'upcoming_appointment' => $upcomingAppt,
            'estimated_hba1c' => $this->estimatedHbA1c($user),
            'alerts' => $this->glucoseAlerts($user),
        ];
    }
}
