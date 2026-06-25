<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MealPlanController extends Controller
{
    public function index(Request $request): View
    {
        $meals = MealPlan::forUser($request->user()->id)
            ->orderBy('plan_date')
            ->get()
            ->groupBy('week_label');

        return view('meals.index', compact('meals'));
    }

    public function show(Request $request, MealPlan $meal): View
    {
        abort_unless($meal->user_id === null || $meal->user_id === $request->user()->id, 403);

        return view('meals.show', compact('meal'));
    }
}
