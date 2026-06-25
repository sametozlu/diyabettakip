<?php

namespace App\Http\Controllers;

use App\Models\InsulinLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsulinLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = $request->user()->insulinLogs()->latest('logged_at')->paginate(15);

        return view('insulin.index', [
            'logs' => $logs,
            'mealTypes' => InsulinLog::MEAL_TYPES,
        ]);
    }

    public function create(): View
    {
        return view('insulin.create', ['mealTypes' => InsulinLog::MEAL_TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'carbs_grams' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'insulin_units' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meal_type' => ['required', 'in:'.implode(',', array_keys(InsulinLog::MEAL_TYPES))],
            'logged_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->insulinLogs()->create($validated);

        return redirect()->route('insulin.index')->with('success', __('Kayıt eklendi.'));
    }

    public function destroy(Request $request, InsulinLog $insulin): RedirectResponse
    {
        abort_unless($insulin->user_id === $request->user()->id, 403);
        $insulin->delete();

        return redirect()->route('insulin.index')->with('success', __('Kayıt silindi.'));
    }
}
