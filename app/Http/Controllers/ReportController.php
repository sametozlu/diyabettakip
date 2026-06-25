<?php

namespace App\Http\Controllers;

use App\Services\HealthAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function pdf(Request $request)
    {
        $user = $request->user()->load('healthProfile');
        $profile = $user->healthProfile;

        $readings = $user->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(30))
            ->orderBy('measured_at')
            ->get();

        $hba1c = $user->hba1cReadings()->latest('tested_at')->get();
        $medications = $user->medications()->where('is_active', true)->get();
        $estimatedHbA1c = $this->analytics->estimatedHbA1c($user);

        $pdf = Pdf::loadView('reports.health-pdf', compact(
            'user', 'profile', 'readings', 'hba1c', 'medications', 'estimatedHbA1c'
        ));

        return $pdf->download('diyabet-raporu-'.now()->format('Y-m-d').'.pdf');
    }
}
