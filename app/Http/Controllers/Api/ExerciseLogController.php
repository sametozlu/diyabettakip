<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExerciseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->exerciseLogs()->latest('logged_at')->paginate(20)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(ExerciseLog::TYPES))],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'steps' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'logged_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $log = $request->user()->exerciseLogs()->create($validated);

        return response()->json($log, 201);
    }

    public function today(Request $request): JsonResponse
    {
        $logs = $request->user()->exerciseLogs()->whereDate('logged_at', today())->get();

        return response()->json([
            'logs' => $logs,
            'total_minutes' => $logs->sum('duration_minutes'),
            'total_steps' => $logs->sum('steps'),
            'goal' => $request->user()->healthProfile?->daily_steps_goal ?? 8000,
        ]);
    }

    public function destroy(Request $request, ExerciseLog $exerciseLog): JsonResponse
    {
        abort_unless($exerciseLog->user_id === $request->user()->id, 403);
        $exerciseLog->delete();

        return response()->json(['message' => 'Silindi']);
    }
}
