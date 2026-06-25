<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodSugarReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BloodSugarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $readings = $request->user()->bloodSugarReadings()
            ->latest('measured_at')
            ->paginate(20);

        return response()->json($readings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'numeric', 'min:20', 'max:600'],
            'context' => ['required', 'in:'.implode(',', array_keys(BloodSugarReading::CONTEXTS))],
            'measured_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'mood' => ['nullable', 'in:'.implode(',', array_keys(BloodSugarReading::MOODS))],
            'sleep_hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'stress_level' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $reading = $request->user()->bloodSugarReadings()->create($validated);

        return response()->json($reading, 201);
    }

    public function show(Request $request, BloodSugarReading $bloodSugar): JsonResponse
    {
        abort_unless($bloodSugar->user_id === $request->user()->id, 403);

        return response()->json($bloodSugar);
    }

    public function update(Request $request, BloodSugarReading $bloodSugar): JsonResponse
    {
        abort_unless($bloodSugar->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'value' => ['sometimes', 'numeric', 'min:20', 'max:600'],
            'context' => ['sometimes', 'in:'.implode(',', array_keys(BloodSugarReading::CONTEXTS))],
            'measured_at' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $bloodSugar->update($validated);

        return response()->json($bloodSugar);
    }

    public function destroy(Request $request, BloodSugarReading $bloodSugar): JsonResponse
    {
        abort_unless($bloodSugar->user_id === $request->user()->id, 403);

        $bloodSugar->delete();

        return response()->json(['message' => 'Silindi']);
    }

    public function weeklyChart(Request $request): JsonResponse
    {
        $data = $request->user()->bloodSugarReadings()
            ->where('measured_at', '>=', now()->subDays(6)->startOfDay())
            ->orderBy('measured_at')
            ->get()
            ->groupBy(fn ($r) => $r->measured_at->format('Y-m-d'))
            ->map(fn ($day, $date) => [
                'date' => $date,
                'label' => \Carbon\Carbon::parse($date)->locale('tr')->isoFormat('ddd'),
                'average' => round($day->avg('value'), 1),
                'readings' => $day->count(),
            ])
            ->values();

        return response()->json([
            'chart' => $data,
            'target_min' => $request->user()->healthProfile?->target_min ?? 70,
            'target_max' => $request->user()->healthProfile?->target_max ?? 140,
        ]);
    }
}
