<?php

namespace App\Http\Controllers;

use App\Models\Hba1cReading;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class Hba1cReadingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $readings = $user->hba1cReadings()
            ->latest('tested_at')
            ->paginate(10);

        $allReadings = $user->hba1cReadings()->orderBy('tested_at')->get();

        $stats = [
            'latest' => $allReadings->last(),
            'average' => $allReadings->avg('value'),
            'total' => $allReadings->count(),
        ];

        return view('hba1c.index', compact('readings', 'allReadings', 'stats'));
    }

    public function create(): View
    {
        return view('hba1c.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'numeric', 'min:3', 'max:20'],
            'tested_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->hba1cReadings()->create($validated);

        return redirect()->route('hba1c.index')->with('success', 'HbA1c kaydı eklendi.');
    }

    public function destroy(Request $request, Hba1cReading $hba1c): RedirectResponse
    {
        abort_unless($hba1c->user_id === $request->user()->id, 403);
        $hba1c->delete();

        return redirect()->route('hba1c.index')->with('success', 'Kayıt silindi.');
    }
}
