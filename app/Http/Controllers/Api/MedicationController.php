<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $medications = $request->user()->medications()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($medications);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'dosage' => ['nullable', 'string', 'max:80'],
            'frequency' => ['required', 'in:'.implode(',', array_keys(Medication::FREQUENCIES))],
            'times' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['times'] = array_values(array_filter($validated['times'] ?? []));

        $medication = $request->user()->medications()->create($validated);

        return response()->json($medication, 201);
    }

    public function today(Request $request): JsonResponse
    {
        $now = now()->format('H:i');
        $medications = $request->user()->medications()
            ->where('is_active', true)
            ->get()
            ->map(function (Medication $med) use ($now) {
                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'dosage' => $med->dosage,
                    'frequency' => $med->frequencyLabel(),
                    'times' => $med->times ?? [],
                    'next_due' => collect($med->times ?? [])->first(fn ($t) => $t >= $now),
                ];
            });

        return response()->json($medications);
    }
}
