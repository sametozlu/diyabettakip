<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsulinLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsulinLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->insulinLogs()->latest('logged_at')->paginate(20)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'carbs_grams' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'insulin_units' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meal_type' => ['required', 'in:'.implode(',', array_keys(InsulinLog::MEAL_TYPES))],
            'logged_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $log = $request->user()->insulinLogs()->create($validated);

        return response()->json($log, 201);
    }

    public function destroy(Request $request, InsulinLog $insulinLog): JsonResponse
    {
        abort_unless($insulinLog->user_id === $request->user()->id, 403);
        $insulinLog->delete();

        return response()->json(['message' => 'Silindi']);
    }
}
