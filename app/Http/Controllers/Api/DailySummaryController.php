<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HealthAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailySummaryController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function today(Request $request): JsonResponse
    {
        return response()->json($this->analytics->dailySummary($request->user()));
    }

    public function alerts(Request $request): JsonResponse
    {
        return response()->json([
            'alerts' => $this->analytics->glucoseAlerts($request->user()),
            'estimated_hba1c' => $this->analytics->estimatedHbA1c($request->user()),
            'avg_glucose_90d' => $this->analytics->averageGlucose90d($request->user()),
        ]);
    }
}
