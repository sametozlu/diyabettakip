<?php

namespace App\Http\Controllers;

use App\Services\HealthAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailySummaryController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function index(Request $request): View
    {
        $summary = $this->analytics->dailySummary($request->user());

        return view('daily-summary.index', compact('summary'));
    }
}
