<?php

namespace App\Http\Controllers;

use App\Models\ShareLink;
use App\Services\HealthAnalyticsService;
use Illuminate\View\View;

class PublicShareController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function show(string $token): View
    {
        $link = ShareLink::where('token', $token)->firstOrFail();

        abort_unless($link->isValid(), 410);

        $link->increment('views');
        $user = $link->user()->with('healthProfile')->first();

        $readings = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(30))
            ->orderBy('measured_at')
            ->get();

        $hba1c = $user->hba1cReadings()->latest('tested_at')->limit(5)->get();
        $medications = $user->medications()->where('is_active', true)->get();
        $estimatedHbA1c = $this->analytics->estimatedHbA1c($user);

        return view('share.public', compact('link', 'user', 'readings', 'hba1c', 'medications', 'estimatedHbA1c'));
    }
}
