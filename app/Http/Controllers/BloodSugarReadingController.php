<?php

namespace App\Http\Controllers;

use App\Models\BloodSugarReading;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BloodSugarReadingController extends Controller
{
    public function index(Request $request): View
    {
        $readings = $request->user()->bloodSugarReadings()
            ->latest('measured_at')
            ->paginate(15);

        return view('blood-sugar.index', [
            'readings' => $readings,
            'contexts' => BloodSugarReading::CONTEXTS,
        ]);
    }

    public function create(): View
    {
        return view('blood-sugar.create', [
            'contexts' => BloodSugarReading::CONTEXTS,
            'moods' => BloodSugarReading::MOODS,
        ]);
    }

    public function store(Request $request): RedirectResponse
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

        $request->user()->bloodSugarReadings()->create($validated);

        return redirect()->route('blood-sugar.index')
            ->with('success', 'Kan şekeri kaydı eklendi.');
    }

    public function destroy(Request $request, BloodSugarReading $bloodSugar): RedirectResponse
    {
        abort_unless($bloodSugar->user_id === $request->user()->id, 403);

        $bloodSugar->delete();

        return redirect()->route('blood-sugar.index')
            ->with('success', 'Kayıt silindi.');
    }
}
