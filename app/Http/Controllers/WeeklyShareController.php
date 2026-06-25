<?php

namespace App\Http\Controllers;

use App\Services\HealthAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeeklyShareController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function story(Request $request): View
    {
        $report = $this->analytics->weeklyReport($request->user());
        $user = $request->user();
        $goal = $user->healthProfile?->water_goal_ml ?? 2000;
        $waterDays = 0;
        for ($i = 0; $i < 7; $i++) {
            $total = $user->waterLogs()->whereDate('logged_at', today()->subDays($i))->sum('amount_ml');
            if ($total >= $goal) {
                $waterDays++;
            }
        }
        $report['water_days'] = $waterDays;

        return view('share.weekly-story', compact('report', 'user'));
    }
}
