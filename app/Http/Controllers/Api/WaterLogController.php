<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaterLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaterLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->waterLogs()->latest('logged_at')->paginate(30)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount_ml' => ['required', 'integer', 'min:50', 'max:2000'],
            'logged_at' => ['nullable', 'date'],
        ]);

        $log = $request->user()->waterLogs()->create([
            'amount_ml' => $validated['amount_ml'],
            'logged_at' => $validated['logged_at'] ?? now(),
        ]);

        return response()->json($log, 201);
    }

    public function today(Request $request): JsonResponse
    {
        $total = $request->user()->waterLogs()->whereDate('logged_at', today())->sum('amount_ml');
        $goal = $request->user()->healthProfile?->water_goal_ml ?? 2000;

        return response()->json([
            'total_ml' => $total,
            'goal_ml' => $goal,
            'percent' => $goal > 0 ? round($total / $goal * 100) : 0,
        ]);
    }

    public function destroy(Request $request, WaterLog $waterLog): JsonResponse
    {
        abort_unless($waterLog->user_id === $request->user()->id, 403);
        $waterLog->delete();

        return response()->json(['message' => 'Silindi']);
    }
}
