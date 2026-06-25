<?php

namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FoodLogController extends Controller
{
    public function __construct(private PushNotificationService $push) {}

    public function index(Request $request): View
    {
        $logs = $request->user()->foodLogs()->latest('logged_at')->paginate(12);

        return view('food.index', [
            'logs' => $logs,
            'mealTypes' => FoodLog::MEAL_TYPES,
        ]);
    }

    public function create(): View
    {
        return view('food.create', ['mealTypes' => FoodLog::MEAL_TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'carbs_grams' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'meal_type' => ['required', 'in:'.implode(',', array_keys(FoodLog::MEAL_TYPES))],
            'logged_at' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $carbs = $validated['carbs_grams'] ?? null;
        $source = 'manual';

        if (! $carbs && ! empty($validated['description'])) {
            $carbs = $this->push->estimateCarbsFromDescription($validated['description']);
            $source = 'estimated';
            $validated['name'] = $validated['name'] ?: $validated['description'];
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('food-photos', 'public');
        }

        $request->user()->foodLogs()->create([
            'name' => $validated['name'],
            'carbs_grams' => $carbs ?? 0,
            'meal_type' => $validated['meal_type'],
            'photo_path' => $photoPath,
            'estimation_source' => $source,
            'logged_at' => $validated['logged_at'],
            'notes' => $validated['notes'] ?? $validated['description'] ?? null,
        ]);

        return redirect()->route('food.index')->with('success', __('Kayıt eklendi.'));
    }

    public function destroy(Request $request, FoodLog $food): RedirectResponse
    {
        abort_unless($food->user_id === $request->user()->id, 403);
        $food->delete();

        return redirect()->route('food.index')->with('success', __('Kayıt silindi.'));
    }
}
