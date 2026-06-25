<?php

namespace App\Http\Controllers;

use App\Models\HealthProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->healthProfile ?? new HealthProfile([
            'target_min' => 70,
            'target_max' => 140,
            'water_goal_ml' => 2000,
            'daily_steps_goal' => 8000,
        ]);

        return view('health-profile.edit', [
            'profile' => $profile,
            'diabetesTypes' => HealthProfile::DIABETES_TYPES,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_min' => ['required', 'integer', 'min:40', 'max:200'],
            'target_max' => ['required', 'integer', 'min:60', 'max:400', 'gte:target_min'],
            'weight' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'height' => ['nullable', 'numeric', 'min:0.5', 'max:2.5'],
            'diabetes_type' => ['nullable', 'string', 'max:50'],
            'doctor_name' => ['nullable', 'string', 'max:120'],
            'water_goal_ml' => ['required', 'integer', 'min:500', 'max:5000'],
            'daily_steps_goal' => ['required', 'integer', 'min:1000', 'max:30000'],
        ]);

        $request->user()->healthProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return redirect()->route('health-profile.edit')
            ->with('success', __('Sağlık profili güncellendi.'));
    }
}
