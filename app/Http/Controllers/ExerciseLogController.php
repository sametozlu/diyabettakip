<?php

namespace App\Http\Controllers;

use App\Models\ExerciseLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExerciseLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = $request->user()->exerciseLogs()->latest('logged_at')->paginate(15);
        $todaySteps = $request->user()->exerciseLogs()->whereDate('logged_at', today())->sum('steps');
        $goal = $request->user()->healthProfile?->daily_steps_goal ?? 8000;

        return view('exercise.index', [
            'logs' => $logs,
            'types' => ExerciseLog::TYPES,
            'todaySteps' => $todaySteps,
            'stepsGoal' => $goal,
        ]);
    }

    public function create(): View
    {
        return view('exercise.create', ['types' => ExerciseLog::TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(ExerciseLog::TYPES))],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'steps' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'logged_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->exerciseLogs()->create($validated);

        return redirect()->route('exercise.index')->with('success', __('Egzersiz kaydedildi.'));
    }

    public function destroy(Request $request, ExerciseLog $exercise): RedirectResponse
    {
        abort_unless($exercise->user_id === $request->user()->id, 403);
        $exercise->delete();

        return redirect()->route('exercise.index')->with('success', __('Kayıt silindi.'));
    }
}
