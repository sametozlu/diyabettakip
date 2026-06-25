<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $appointments = $request->user()->appointments()
            ->orderBy('scheduled_at')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        return view('appointments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_name' => ['required', 'string', 'max:120'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'location' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->appointments()->create($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Randevu eklendi.');
    }

    public function edit(Request $request, Appointment $appointment): View
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        return view('appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'doctor_name' => ['required', 'string', 'max:120'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Randevu güncellendi.');
    }

    public function destroy(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Randevu silindi.');
    }
}
