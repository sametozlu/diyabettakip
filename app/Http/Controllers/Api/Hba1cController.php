<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hba1cReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Hba1cController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->hba1cReadings()->latest('tested_at')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'numeric', 'min:3', 'max:20'],
            'tested_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $reading = $request->user()->hba1cReadings()->create($validated);

        return response()->json($reading, 201);
    }

    public function destroy(Request $request, Hba1cReading $hba1c): JsonResponse
    {
        abort_unless($hba1c->user_id === $request->user()->id, 403);
        $hba1c->delete();

        return response()->json(['message' => 'Silindi']);
    }
}
