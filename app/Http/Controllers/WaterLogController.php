<?php

namespace App\Http\Controllers;

use App\Models\WaterLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaterLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = $request->user()->waterLogs()->latest('logged_at')->paginate(20);
        $todayTotal = $request->user()->waterLogs()->whereDate('logged_at', today())->sum('amount_ml');
        $goal = $request->user()->healthProfile?->water_goal_ml ?? 2000;

        return view('water.index', compact('logs', 'todayTotal', 'goal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount_ml' => ['required', 'integer', 'min:50', 'max:2000'],
            'logged_at' => ['nullable', 'date'],
        ]);

        $request->user()->waterLogs()->create([
            'amount_ml' => $validated['amount_ml'],
            'logged_at' => $validated['logged_at'] ?? now(),
        ]);

        return redirect()->route('water.index')->with('success', __('Su kaydı eklendi.'));
    }

    public function destroy(Request $request, WaterLog $water): RedirectResponse
    {
        abort_unless($water->user_id === $request->user()->id, 403);
        $water->delete();

        return redirect()->route('water.index')->with('success', __('Kayıt silindi.'));
    }
}
