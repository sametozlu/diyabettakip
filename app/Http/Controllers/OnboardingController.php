<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->healthProfile?->onboarding_done) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index');
    }

    public function complete(Request $request): RedirectResponse
    {
        $request->user()->healthProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['onboarding_done' => true, 'target_min' => 70, 'target_max' => 140]
        );

        return redirect()->route('dashboard')->with('success', 'Hoş geldin! Sağlık yolculuğun başladı.');
    }
}
