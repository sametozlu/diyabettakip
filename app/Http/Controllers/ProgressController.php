<?php

namespace App\Http\Controllers;

use App\Models\ProgressSnapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $snapshots = $request->user()->progressSnapshots()->orderByDesc('recorded_at')->get();
        $weight = $snapshots->where('type', 'weight');
        $hba1c = $snapshots->where('type', 'hba1c');

        return view('progress.index', [
            'snapshots' => $snapshots,
            'weightHistory' => $weight,
            'hba1cHistory' => $hba1c,
            'types' => ProgressSnapshot::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(ProgressSnapshot::TYPES))],
            'value' => ['required', 'numeric', 'min:0'],
            'recorded_at' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('progress', 'public')
            : null;

        $request->user()->progressSnapshots()->create([
            ...$validated,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('progress.index')->with('success', 'İlerleme kaydı eklendi.');
    }

    public function destroy(Request $request, ProgressSnapshot $progress): RedirectResponse
    {
        abort_unless($progress->user_id === $request->user()->id, 403);
        $progress->delete();

        return redirect()->route('progress.index')->with('success', 'Kayıt silindi.');
    }
}
