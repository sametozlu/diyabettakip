<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user()->healthProfile);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_min' => ['sometimes', 'integer', 'min:40', 'max:200'],
            'target_max' => ['sometimes', 'integer', 'min:60', 'max:400'],
            'weight' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'height' => ['nullable', 'numeric', 'min:0.5', 'max:2.5'],
            'diabetes_type' => ['nullable', 'string', 'max:50'],
            'doctor_name' => ['nullable', 'string', 'max:120'],
            'water_goal_ml' => ['sometimes', 'integer', 'min:500', 'max:5000'],
            'daily_steps_goal' => ['sometimes', 'integer', 'min:1000', 'max:30000'],
        ]);

        $profile = $request->user()->healthProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json($profile);
    }
}
