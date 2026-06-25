<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appointments = $request->user()->appointments()
            ->orderBy('scheduled_at')
            ->get();

        return response()->json($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_name' => ['required', 'string', 'max:120'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'location' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment = $request->user()->appointments()->create($validated);

        return response()->json($appointment, 201);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        return response()->json($appointment);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'doctor_name' => ['sometimes', 'string', 'max:120'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'scheduled_at' => ['sometimes', 'date'],
            'location' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment->update($validated);

        return response()->json($appointment);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        $appointment->delete();

        return response()->json(['message' => 'Silindi']);
    }

    public function upcoming(Request $request): JsonResponse
    {
        $appointments = $request->user()->appointments()
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $reminders = $appointments->filter(fn ($a) => $a->needsReminder())->values();

        return response()->json([
            'upcoming' => $appointments,
            'reminders' => $reminders,
        ]);
    }
}
