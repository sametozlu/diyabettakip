<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $meals = MealPlan::forUser($request->user()->id)
            ->orderBy('plan_date')
            ->get();

        return response()->json($meals);
    }

    public function show(Request $request, MealPlan $meal): JsonResponse
    {
        abort_unless($meal->user_id === null || $meal->user_id === $request->user()->id, 403);

        return response()->json($meal);
    }

    public function today(Request $request): JsonResponse
    {
        $meal = MealPlan::forUser($request->user()->id)
            ->whereDate('plan_date', today())
            ->first();

        return response()->json($meal ?? ['message' => 'Bugün için plan yok']);
    }
}
