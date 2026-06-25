<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicationController extends Controller
{
    public function index(Request $request): View
    {
        $medications = $request->user()->medications()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('medications.index', [
            'medications' => $medications,
            'frequencies' => Medication::FREQUENCIES,
        ]);
    }

    public function create(): View
    {
        return view('medications.create', [
            'frequencies' => Medication::FREQUENCIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'dosage' => ['nullable', 'string', 'max:80'],
            'frequency' => ['required', 'in:'.implode(',', array_keys(Medication::FREQUENCIES))],
            'times' => ['nullable', 'array'],
            'times.*' => ['string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['times'] = array_values(array_filter($validated['times'] ?? []));

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('medications', 'public');
        }
        unset($validated['photo']);

        $request->user()->medications()->create($validated);

        return redirect()->route('medications.index')->with('success', 'İlaç eklendi.');
    }

    public function edit(Request $request, Medication $medication): View
    {
        abort_unless($medication->user_id === $request->user()->id, 403);

        return view('medications.edit', [
            'medication' => $medication,
            'frequencies' => Medication::FREQUENCIES,
        ]);
    }

    public function update(Request $request, Medication $medication): RedirectResponse
    {
        abort_unless($medication->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'dosage' => ['nullable', 'string', 'max:80'],
            'frequency' => ['required', 'in:'.implode(',', array_keys(Medication::FREQUENCIES))],
            'times' => ['nullable', 'array'],
            'times.*' => ['string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['times'] = array_values(array_filter($validated['times'] ?? []));

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('medications', 'public');
        }
        unset($validated['photo']);

        $medication->update($validated);

        return redirect()->route('medications.index')->with('success', 'İlaç güncellendi.');
    }

    public function destroy(Request $request, Medication $medication): RedirectResponse
    {
        abort_unless($medication->user_id === $request->user()->id, 403);
        $medication->delete();

        return redirect()->route('medications.index')->with('success', 'İlaç silindi.');
    }
}
