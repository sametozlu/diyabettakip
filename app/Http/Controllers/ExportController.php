<?php

namespace App\Http\Controllers;

use App\Models\BloodSugarReading;
use App\Services\HealthAnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function json(Request $request)
    {
        $user = $request->user()->load('healthProfile');

        $data = [
            'exported_at' => now()->toIso8601String(),
            'user' => ['name' => $user->name, 'email' => $user->email],
            'health_profile' => $user->healthProfile,
            'blood_sugar' => $user->bloodSugarReadings()->orderBy('measured_at')->get(),
            'hba1c' => $user->hba1cReadings()->orderBy('tested_at')->get(),
            'medications' => $user->medications()->get(),
            'appointments' => $user->appointments()->orderBy('scheduled_at')->get(),
            'insulin_logs' => $user->insulinLogs()->orderBy('logged_at')->get(),
            'exercise_logs' => $user->exerciseLogs()->orderBy('logged_at')->get(),
            'water_logs' => $user->waterLogs()->orderBy('logged_at')->get(),
            'estimated_hba1c' => $this->analytics->estimatedHbA1c($user),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="diyabet-veri-'.now()->format('Y-m-d').'.json"',
        ]);
    }

    public function csv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $filename = 'diyabet-glukoz-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['tarih', 'deger_mg_dl', 'tip', 'not', 'ruh_hali', 'uyku_saat', 'stres_1_5']);

            $user->bloodSugarReadings()->orderBy('measured_at')->each(function (BloodSugarReading $r) use ($handle) {
                fputcsv($handle, [
                    $r->measured_at->format('Y-m-d H:i'),
                    $r->value,
                    $r->context,
                    $r->notes,
                    $r->mood,
                    $r->sleep_hours,
                    $r->stress_level,
                ]);
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
